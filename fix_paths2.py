import os
import re

dirs = ['controllers', 'models', 'views', 'includes', 'handler']

fixed_files = []

for folder in dirs:
    if not os.path.exists(folder):
        continue
    for filename in os.listdir(folder):
        if not filename.endswith('.php'):
            continue
        filepath = os.path.join(folder, filename)
        with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()

        original = content

        # Fix require_once with DOCUMENT_ROOT + /POSu/subfolder/file.php
        # Pattern: $_SERVER['DOCUMENT_ROOT'] . '/POSu/models/SomeModel.php'
        # Should become: __DIR__ . '/../models/SomeModel.php'
        def fix_require(m):
            full_path = m.group(1)  # e.g. models/ActivityLogModel.php or controllers/ActivityLogController.php
            return f"__DIR__ . '/../{full_path}'"

        content = re.sub(
            r"\$_SERVER\['DOCUMENT_ROOT'\]\s*\.\s*['\"]\/POSu\/([^'\"]+)['\"]",
            fix_require,
            content
        )

        # Also fix any leftover __DIR__ . '/../ActivityLogModel.php' (missing subfolder)
        # These are cases where the subfolder got dropped - we need to detect them
        # by checking if the file exists in models/
        def fix_missing_subdir(m):
            filename_only = m.group(1)
            # Check common locations
            for subdir in ['models', 'controllers', 'views', 'includes']:
                if os.path.exists(os.path.join(subdir, filename_only)):
                    return f"__DIR__ . '/../{subdir}/{filename_only}'"
            return m.group(0)  # leave unchanged if can't determine

        content = re.sub(
            r"__DIR__ \. '\/\.\.\/([A-Za-z][A-Za-z0-9_]+\.php)'",
            fix_missing_subdir,
            content
        )

        # Fix Location headers
        content = re.sub(r"Location: /POSu/", "Location: /", content)
        content = re.sub(r"Location: http://localhost/POSu/", "Location: /", content)

        # Fix hardcoded backup path
        content = content.replace('C:/xampp/htdocs/POSu/backups/', '/app/backups/')

        if content != original:
            with open(filepath, 'w', encoding='utf-8') as f:
                f.write(content)
            fixed_files.append(filepath)
            print(f"Fixed: {filepath}")

print(f"\nTotal fixed: {len(fixed_files)} files")
print("Now run: git add . && git commit -m 'fix require paths' && git push")
