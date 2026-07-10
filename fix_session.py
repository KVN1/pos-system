import os, re

# Run from D:\xampp\htdocs\POSu
# Replaces bare session_start() in views with a safe version

fixed = []
skip = {'.git', 'backups', 'backup', 'vendor'}

for root, dirs, files in os.walk('views'):
    for f in files:
        if not f.endswith('.php'):
            continue
        fp = os.path.join(root, f)
        try:
            with open(fp, 'r', encoding='utf-8', errors='ignore') as fh:
                content = fh.read()
            nc = content

            # Replace bare session_start() with safe version
            nc = nc.replace(
                'session_start();',
                'if (session_status() === PHP_SESSION_NONE) { session_start(); }'
            )

            if nc != content:
                with open(fp, 'w', encoding='utf-8') as fh:
                    fh.write(nc)
                fixed.append(fp)
                print(f"Fixed: {fp}")
        except Exception as e:
            print(f"Error {fp}: {e}")

print(f"\nTotal: {len(fixed)} files fixed")
