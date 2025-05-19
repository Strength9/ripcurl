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

function toPascalCase(str) {
  return str.replace(/(^\w|[-_]\w)/g, m => m.replace(/[-_]/, '').toUpperCase());
}

function replacePlaceholders(content, replacements) {
  let result = content;
  for (const [search, replace] of Object.entries(replacements)) {
    result = result.replace(new RegExp(search, 'g'), replace);
  }
  return result;
}

const templates = {
  'block.json': `{
  "$schema": "https://schemas.wp.org/trunk/block.json",
  "apiVersion": 3,
  "name": "ripcurl/{{blockName}}",
  "version": "0.1.0",
  "title": "{{blockTitle}}",
  "category": "{{blockCategory}}",
  "icon": "smiley",
  "description": "{{blockTitle}} block scaffolded with Create Block tool.",
  "example": {},
  "supports": {
    "html": false
  },
  "textdomain": "{{blockName}}",
  "editorScript": "file:./index.js",
  "editorStyle": "file:./index.css",
  "style": "file:./style-index.css",
  "viewScript": "file:./view.js"
}
`,
  'edit.js': `/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit() {
	return (
		<p { ...useBlockProps() }>
			{ __( 'Todo List – hello from the editor!', '{{blockName}}' ) }
		</p>
	);
}
`,
  'index.js': `/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * All files containing \`style\` keyword are bundled together. The code used
 * gets applied both to the front of your site and to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './style.scss';

/**
 * Internal dependencies
 */
import Edit from './edit';
import save from './save';
import metadata from './block.json';

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
registerBlockType( metadata.name, {
	/**
	 * @see ./edit.js
	 */
	edit: Edit,

	/**
	 * @see ./save.js
	 */
	save,
} );
`,
  'save.js': `/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * The save function defines the way in which the different attributes should
 * be combined into the final markup, which is then serialized by the block
 * editor into \`post_content\`.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#save
 *
 * @return {Element} Element to render.
 */
export default function save() {
	return (
		<p { ...useBlockProps.save() }>
			{ 'Todo List – hello from the saved content!' }
		</p>
	);
}
`,
  'view.js': `/**
 * Use this file for JavaScript code that you want to run in the front-end
 * on posts/pages that contain this block.
 *
 * When this file is defined as the value of the \`viewScript\` property
 * in \`block.json\` it will be enqueued on the front end of the site.
 *
 * Example:
 *
 * {
 *   "viewScript": "file:./view.js"
 * }
 *
 * If you're not making any changes to this file because your project doesn't need any
 * JavaScript running in the front-end, then you should delete this file and remove
 * the \`viewScript\` property from \`block.json\`.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/#view-script
 */

/* eslint-disable no-console */
console.log( 'Hello World! (from create-block-{{blockName}} block)' );
/* eslint-enable no-console */
`,
  'editor.scss': `/**
 * The following styles get applied inside the editor only.
 *
 * Replace them with your own styles or remove the file completely.
 */

.wp-block-create-block-{{blockName}} {
	border: 1px dotted #f00;
}
`,
  'style.scss': `/**
 * The following styles get applied both on the front of your site
 * and in the editor.
 *
 * Replace them with your own styles or remove the file completely.
 */

.wp-block-create-block-{{blockName}} {
	background-color: #21759b;
	color: #fff;
	padding: 2px;
}
`,
};

(async () => {
  const blockName = await prompt('✔ Block name (kebab-case): ');
  const blockTitle = await prompt('✔ Block title: ');
  const blockCategory = await prompt('✔ Block category (default: ripcurlblocks): ') || 'ripcurlblocks';

  const blockDir = path.join(srcDir, blockName);
  if (fs.existsSync(blockDir)) {
    console.error(`✘ Block "${blockName}" already exists.`);
    rl.close();
    return;
  }

  // Prepare replacements for placeholders
  const replacements = {
    '{{blockName}}': blockName,
    '{{blockTitle}}': blockTitle,
    '{{blockCategory}}': blockCategory,
  };

  // Create directory
  await fs.ensureDir(blockDir);

  // Write all template files
  for (const [filename, template] of Object.entries(templates)) {
    const content = replacePlaceholders(template, replacements);
    await fs.writeFile(path.join(blockDir, filename), content, 'utf8');
  }

  console.log(`✔ Block "${blockName}" scaffolded successfully under category "${blockCategory}".`);
  rl.close();
})();
