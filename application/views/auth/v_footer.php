</div>
</div>

<!-- 

FOOTER TEMPLATE + JS GLOBAL
File: v_footer.php
Fungsi: Load jQuery + Bootstrap + Modal dinamis + JS sidebar toggle
Dipanggil di: Semua view via $this->_render()

-->

<!-- CDN jQuery + Bootstrap JS dari folder assets -->
<!-- JQuery wajib duluan baru Bootstrap, urutan ga boleh kebalik -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>



<!-- JS Toggle Sidebar Menu -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const btn = document.getElementById("menu-toggle");
        const wrapper = document.getElementById("wrapper");

        // Cek dulu element ada apa ga biar ga error JS
        if(btn && wrapper) {
            btn.addEventListener("click", function (e) {
                e.preventDefault();
                wrapper.classList.toggle("toggled"); // Class 'toggled' ngatur CSS sidebar
            });
        }
    });
</script>

</body>
</html>
<!-- End of file v_footer.php -->