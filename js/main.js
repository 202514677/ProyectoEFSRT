document.addEventListener("DOMContentLoaded", function() {
    const menuToggle = document.getElementById("menuToggle");
    const sidebar = document.getElementById("sidebar");

    if(menuToggle && sidebar) {
        menuToggle.addEventListener("click", function() {
            sidebar.classList.toggle("active");
        });
    }

    const deleteButtons = document.querySelectorAll(".btn-delete");
    deleteButtons.forEach(button => {
        button.addEventListener("click", function(e) {
            if(!confirm("¿Está seguro de realizar esta acción en el sistema?")) {
                e.preventDefault();
            }
        });
    });
});