const fg = require('fast-glob');
const fs = require('fs-extra');
const path = require('path');

const srcRoot = path.join(__dirname, '..', 'src');
const buildRoot = path.join(__dirname, '..', 'build');

async function copyFiles() {
  const filesToCopy = await fg(['*/render.php', '*/assets/**'], {
    cwd: srcRoot,
    onlyFiles: false
  });

  for (const relativePath of filesToCopy) {
    const srcPath = path.join(srcRoot, relativePath);
    const destPath = path.join(buildRoot, relativePath);
    await fs.copy(srcPath, destPath);
    console.log(`✓ Copied ${relativePath}`);
  }
}

copyFiles().catch(err => {
  console.error("Error copying block assets:", err);
  process.exit(1);
});
