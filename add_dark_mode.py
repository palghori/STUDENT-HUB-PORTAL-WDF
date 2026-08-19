import os
import glob

base_dir = '/Users/palghori/Documents/STUDENT HUB PORTAL WDF/practical 4'
pages_dir = os.path.join(base_dir, 'PAGES')
css_file = os.path.join(base_dir, 'CSS/script.css')
js_file = os.path.join(base_dir, 'java.js')

# 1. Update java.js
with open(js_file, 'r') as f:
    js_content = f.read()

if 'toggleDarkMode' not in js_content:
    with open(js_file, 'a') as f:
        f.write('''

function toggleDarkMode() {
    const isDark = document.body.classList.toggle('dark-mode');
    localStorage.setItem('darkMode', isDark ? 'enabled' : 'disabled');
}

// Apply dark mode on page load
document.addEventListener('DOMContentLoaded', () => {
    if (localStorage.getItem('darkMode') === 'enabled') {
        document.body.classList.add('dark-mode');
    }
});
''')

# 2. Update script.css
with open(css_file, 'r') as f:
    css_content = f.read()

if '.dark-mode' not in css_content:
    with open(css_file, 'a') as f:
        f.write('''

/* Dark mode styles */
body.dark-mode {
  background: linear-gradient(135deg, #1e1e1e 0%, #121212 100%);
  color: #e0e0e0;
}
body.dark-mode .navbar {
  background: linear-gradient(90deg, #333, #222);
}
body.dark-mode .hero,
body.dark-mode .card,
body.dark-mode .content-box,
body.dark-mode .form-box {
  background: #2a2a2a;
  border-color: #444;
}
body.dark-mode .hero h1,
body.dark-mode .hero h2,
body.dark-mode .card h2,
body.dark-mode .content-box h2,
body.dark-mode .form-box h2 {
  color: #f0a060;
}
body.dark-mode .feature-box {
  background: #333;
  border-color: #555;
}
body.dark-mode .feature-box h3 {
  color: #f0a060;
}
body.dark-mode p,
body.dark-mode ul,
body.dark-mode li {
  color: #ccc;
}
body.dark-mode label {
  color: #ccc;
}
body.dark-mode input,
body.dark-mode textarea,
body.dark-mode select {
  background: #333;
  color: #fff;
  border-color: #555;
}
body.dark-mode th,
body.dark-mode td {
  border-color: #444;
  background-color: #2a2a2a;
  color: #eee;
}
body.dark-mode th {
  background-color: #333;
}
body.dark-mode .nav-links a,
body.dark-mode .hero>p a {
  color: #f0a060;
}
body.dark-mode .hero a,
body.dark-mode a {
  color: #e0a060;
}
body.dark-mode .toggle-btn {
  background: #f0a060;
  color: #121212;
}
''')

# 3. Update all HTML files
html_files = glob.glob(os.path.join(pages_dir, '*.html'))

for html_file in html_files:
    with open(html_file, 'r') as f:
        html = f.read()
    
    modified = False
    
    # Add button to nav-links
    if 'Toggle Dark Mode' not in html:
        nav_links_index = html.find('<div class="nav-links">')
        if nav_links_index != -1:
            end_div_index = html.find('</div>', nav_links_index)
            if end_div_index != -1:
                button_html = '\\n        <button class="toggle-btn" onclick="toggleDarkMode()" style="width: auto; padding: 5px 10px; margin-left: 10px; border-radius: 5px; cursor: pointer; font-weight: bold;">Toggle Dark Mode</button>'
                html = html[:end_div_index] + button_html + '\\n      ' + html[end_div_index:]
                modified = True
    
    # Add java.js if missing
    if 'java.js' not in html:
        body_end_index = html.find('</body>')
        if body_end_index != -1:
            script_html = '<script src="../java.js"></script>\\n  '
            html = html[:body_end_index] + script_html + html[body_end_index:]
            modified = True
            
    if modified:
        with open(html_file, 'w') as f:
            f.write(html)
        print(f"Updated {os.path.basename(html_file)}")
