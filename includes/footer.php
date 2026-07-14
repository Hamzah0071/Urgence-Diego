<script>
        function toggleMobileMenu() {
            document.getElementById('topNav').classList.toggle('open');
            document.getElementById('navBackdrop').classList.toggle('open');
        }

        // Ferme le menu automatiquement quand on clique un lien (mobile)
        document.querySelectorAll('#topNav a').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth < 768) {
                    document.getElementById('topNav').classList.remove('open');
                    document.getElementById('navBackdrop').classList.remove('open');
                }
            });
        });
    </script>
</body>
</html>