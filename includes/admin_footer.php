</main>

    </div>

    <script>
        function toggleAdminMenu() {
            document.getElementById('adminSidebar').classList.toggle('open');
            document.getElementById('sidebarBackdrop').classList.toggle('open');
        }

        // Ferme le menu automatiquement après avoir choisi un lien (mobile)
        document.querySelectorAll('#adminSidebar .nav-links a').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth < 992) {
                    document.getElementById('adminSidebar').classList.remove('open');
                    document.getElementById('sidebarBackdrop').classList.remove('open');
                }
            });
        });
    </script>
</body>
</html>