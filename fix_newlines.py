import os
import glob

pages_dir = '/Users/palghori/Documents/STUDENT HUB PORTAL WDF/practical 4/PAGES'
html_files = glob.glob(os.path.join(pages_dir, '*.html'))

for html_file in html_files:
    with open(html_file, 'r') as f:
        html = f.read()
    
    if '\\n' in html:
        # replace literal '\n' string with actual newline '\n'
        html = html.replace('\\n', '\n')
        with open(html_file, 'w') as f:
            f.write(html)
        print(f"Fixed {os.path.basename(html_file)}")
