import os

# Run this from your POSu root folder
# Fixes ALL /POSu/ references in ALL files

fixed = []
skipped_dirs = {'.git', 'backups', 'vendor'}
extensions = ('.php', '.html', '.css', '.js')

for root, dirs, files in os.walk('.'):
    # Skip unwanted dirs
    dirs[:] = [d for d in dirs if d not in skipped_dirs]
    
    for filename in files:
        if not filename.endswith(extensions):
            continue
            
        filepath = os.path.join(root, filename)
        
        try:
            with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
                content = f.read()
        except:
            continue
            
        original = content
        
        # Fix ALL /POSu/ references
        content = content.replace('/POSu/', '/')
        content = content.replace('http://localhost/POSu/', '/')
        content = content.replace("'POSu/", "'")
        content = content.replace('"POSu/', '"')
        
        # Fix register link specifically
        content = content.replace(
            '/views/register.php',
            '/user/register'
        )
        content = content.replace(
            '/views/forgotpass.php',
            '/user/forgotpass'
        )
        
        if content != original:
            with open(filepath, 'w', encoding='utf-8') as f:
                f.write(content)
            fixed.append(filepath)
            print(f"Fixed: {filepath}")

print(f"\nTotal: {len(fixed)} files fixed")
print("Now run: git add . && git commit -m 'fix all paths' && git push")
