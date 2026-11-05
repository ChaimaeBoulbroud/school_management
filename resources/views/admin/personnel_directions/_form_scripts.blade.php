{{-- resources/views/admin/personnel_directions/_form_scripts.blade.php --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const createUserCheckboxes = document.querySelectorAll('input[name="create_user_account"]'); // Could be one or the other id
    const userAccountFieldsCollapseEl = document.getElementById('user_account_fields_collapse_personnel');
    const emailRequiredStar = document.getElementById('email_required_star_personnel');
    const passwordRequiredStar = document.getElementById('password_required_star_personnel');

    if (userAccountFieldsCollapseEl) {
        // Ensure Bootstrap JS is loaded for this to work, or use manual show/hide
        const bsUserAccountCollapse = new bootstrap.Collapse(userAccountFieldsCollapseEl, {
             toggle: false // Initialize based on 'show' class or checkbox state later
        });

        function toggleRequiredStars(show) {
            if(emailRequiredStar) emailRequiredStar.classList.toggle('d-none', !show);
            if(passwordRequiredStar) passwordRequiredStar.classList.toggle('d-none', !show);
        }

        // Handle initial state based on whether the collapse element should be shown
        let shouldShowCollapseInitially = userAccountFieldsCollapseEl.classList.contains('show');
        if (!shouldShowCollapseInitially) {
            createUserCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    shouldShowCollapseInitially = true;
                }
            });
        }
        if (shouldShowCollapseInitially) {
             bsUserAccountCollapse.show(); // Make sure it's shown if it has 'show' or checkbox is checked
             toggleRequiredStars(true);
        } else {
             toggleRequiredStars(false);
        }


        createUserCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                if (this.checked) {
                    bsUserAccountCollapse.show();
                    toggleRequiredStars(true);
                } else {
                    bsUserAccountCollapse.hide();
                    toggleRequiredStars(false);
                }
            });
        });
    }
});
</script>