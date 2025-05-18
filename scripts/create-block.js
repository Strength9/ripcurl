const fs = require('fs-extra');
const path = require('path');
const readline = require('readline');

const srcDir = path.resolve(__dirname, '../src');

const rl = readline.createInterface({
  input: process.stdin,
  output: process.stdout
});

function prompt(query) {
  return new Promise(resolve => rl.question(query, resolve));
}

(async () => {
  const blockName = await prompt('✔ Block name (kebab-case): ');
  const blockTitle = await prompt('✔ Block title: ');
  const blockCategory = await prompt('✔ Block category (default: ripcurl): ') || 'ripcurl';
  const useRender = (await prompt('✔ Use render.php? (y/N): ')).toLowerCase() === 'y';

  const blockDir = path.join(srcDir, blockName);
  if (fs.existsSync(blockDir)) {
    console.error(`✘ Block "${blockName}" already exists.`);
    rl.close();
    return;
  }

  // Create directory structure
  await fs.ensureDir(path.join(blockDir, 'assets'));

  // Create block.json
  const blockJson = {
    apiVersion: 2,
    name: `ripcurl/${blockName}`,
    title: blockTitle,
    category: blockCategory,
    icon: "smiley",
    description: `A custom block: ${blockTitle}`,
    keywords: [blockName],
    textdomain: "ripcurl",
    editorScript: "file:./index.js",
    editorStyle: "file:./editor.css",
    style: "file:./style.css",
    supports: {
      html: false
    }
  };
  await fs.writeJson(path.join(blockDir, 'block.json'), blockJson, { spaces: 2 });

  // Create edit.js
  const editJs = `import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import './editor.scss';
import './style.scss';

export default function Edit() {
  return (
    <div {...useBlockProps()}>
      <p>{__('Hello from ${blockTitle}', 'ripcurl')}</p>
    </div>
  );
}
`;
  await fs.writeFile(path.join(blockDir, 'edit.js'), editJs);

  // Create SCSS files
  const styleScss = `.wp-block-ripcurl-${blockName} {
  padding: 1rem;
  background: #f0f0f0;
}`;
  const editorScss = `.wp-block-ripcurl-${blockName} {
  outline: 2px dashed #ccc;
}`;
  await fs.writeFile(path.join(blockDir, 'style.scss'), styleScss);
  await fs.writeFile(path.join(blockDir, 'editor.scss'), editorScss);

  // Optional render.php
  if (useRender) {
    const renderPhp = `<?php
/**
 * Render callback for ripcurl/${blockName}
 */
?>
<div class="wp-block-ripcurl-${blockName}">
  <p>Hello from <strong>${blockTitle}</strong> (rendered in PHP).</p>
</div>
`;
    await fs.writeFile(path.join(blockDir, 'render.php'), renderPhp);
  }

  console.log(`✔ Block "${blockName}" scaffolded successfully under category "${blockCategory}".`);
  rl.close();
})();
