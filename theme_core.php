<style>
    :root {
        --bg-body: #f8fafc;
        --bg-sidebar: #0f172a;
        --sidebar-text: #94a3b8;
        --sidebar-hover: #1e3a8a;
        --bg-header: #ffffff;
        --text-main: #1e293b;
        --card-bg: #ffffff;
        --card-border: #e2e8f0;
        --table-head: #f1f5f9;
        --table-text: #475569;
        --banner-bg: linear-gradient(135deg, #e0f2fe, #bae6fd);
        --banner-text: #0369a1;
        --banner-border: #7dd3fc;
        --marquee-bg: rgba(255, 255, 255, 0.6);
    }
    [data-theme="dark"] {
        --bg-body: #0b0f19;
        --bg-sidebar: #020617;
        --sidebar-text: #64748b;
        --sidebar-hover: #1e293b;
        --bg-header: #111827;
        --text-main: #f1f5f9;
        --card-bg: #111827;
        --card-border: #1f2937;
        --table-head: #1f2937;
        --table-text: #9ca3af;
        --banner-bg: linear-gradient(135deg, #1e293b, #0f172a);
        --banner-text: #38bdf8;
        --banner-border: #334155;
        --marquee-bg: rgba(0, 0, 0, 0.3);
    }
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, sans-serif; transition: background 0.3s, color 0.3s, border-color 0.3s; }
</style>

<script>
    // Langsung aktifkan tema yang tersimpan pas halaman mulai dimuat
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);

    // Fungsi saklar utama
    function toggleTheme() {
        let currentTheme = document.documentElement.getAttribute('data-theme');
        let newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        
        // Update ikon tombol otomatis
        updateIcon(newTheme);
    }

    function updateIcon(theme) {
        const btn = document.getElementById('themeToggle');
        if (btn) {
            btn.innerHTML = theme === 'dark' ? '☀️ Light Mode' : '🌙 Dark Mode';
        }
    }

    // Pas halaman selesai dimuat, sesuaikan tampilan tombolnya
    window.addEventListener('DOMContentLoaded', () => {
        const currentTheme = localStorage.getItem('theme') || 'light';
        updateIcon(currentTheme);
    });
</script>
