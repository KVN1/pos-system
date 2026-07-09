import os
import re

# Directories to fix
dirs = ['controllers', 'models', 'views', 'includes', 'handler']

# Base directory - run this script from your POSu folder
base = os.getcwd()

fixed_files = []
total_replacements = 0

for folder in dirs:
    folder_path = os.path.join(base, folder)
    if not os.path.exists(folder_path):
        continue
    for filename in os.listdir(folder_path):
        if not filename.endswith('.php'):
            continue
        filepath = os.path.join(folder_path, filename)
        with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()

        original = content

        # Fix 1: require_once with DOCUMENT_ROOT + /POSu/
        # $_SERVER['DOCUMENT_ROOT'] . '/POSu/models/...' -> __DIR__ . '/../models/...'
        def fix_require(m):
            path = m.group(1)  # e.g. /POSu/models/ActivityLogModel.php
            # Remove /POSu/ prefix
            path = re.sub(r'^/POSu/', '/', path)
            # Determine relative path from controllers/ to the target
            parts = path.strip('/').split('/')
            # parts[0] = models/views/controllers etc, parts[1:] = rest
            target_dir = parts[0]
            target_file = '/'.join(parts[1:])

            # Current file is in 'controllers' folder
            # Need relative path: __DIR__ . '/../models/File.php'
            return f"__DIR__ . '/../{target_dir}/{target_file}'"

        content = re.sub(
            r"\$_SERVER\['DOCUMENT_ROOT'\]\s*\.\s*'/POSu/([^']+)'",
            lambda m: f"__DIR__ . '/../{m.group(1).split('/', 1)[1] if '/' in m.group(1) else m.group(1)}'",
            content
        )

        # Fix 2: header Location with /POSu/ prefix
        # header("Location: /POSu/...") -> header("Location: /...")
        content = content.replace("Location: /POSu/", "Location: /")

        # Fix 3: hardcoded localhost path
        content = content.replace(
            "Location: http://localhost/POSu/",
            "Location: /"
        )

        # Fix 4: hardcoded C:/xampp path
        content = content.replace(
            'C:/xampp/htdocs/POSu/backups/',
            __import__('os').path.join(os.getcwd(), 'backups') + '/'
        )
        # For Railway, use relative path
        content = content.replace(
            'C:/xampp/htdocs/POSu/backups/',
            '/app/backups/'
        )

        if content != original:
            count = original.count('/POSu/')
            total_replacements += count
            with open(filepath, 'w', encoding='utf-8') as f:
                f.write(content)
            fixed_files.append(f"{folder}/{filename} ({count} fixes)")

print(f"Fixed {len(fixed_files)} files, {total_replacements} replacements:")
for f in fixed_files:
    print(f"  - {f}")
print("\nDone! Now run: git add . && git commit -m 'fix paths for Railway' && git push")
