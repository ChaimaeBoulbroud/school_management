{{-- resources/views/admin/enseignants/_form_scripts.blade.php --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Target both potential checkboxes (one for create, one for edit if no user yet)
    const createUserCheckboxes = document.querySelectorAll('input[name="create_user_account"]');
    const userAccountFieldsCollapseEl = document.getElementById('user_account_fields_collapse_enseignant');
    const emailRequiredStar = document.getElementById('email_required_star_enseignant');
    const passwordRequiredStar = document.getElementById('password_required_star_enseignant');

    if (userAccountFieldsCollapseEl) {
        const bsUserAccountCollapse = new bootstrap.Collapse(userAccountFieldsCollapseEl, {
             toggle: false // Initialize as collapsed, will be shown if 'show' class is present or checkbox is checked
        });

        function toggleRequiredStars(show) {
            if(emailRequiredStar) emailRequiredStar.classList.toggle('d-none', !show);
            if(passwordRequiredStar) passwordRequiredStar.classList.toggle('d-none', !show);
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
            // Initial state based on old input or if the collapse div should be shown by default (edit mode with user)
            if (checkbox.checked || userAccountFieldsCollapseEl.classList.contains('show')) {
                // If checkbox is already checked (e.g. from old input), ensure stars are visible.
                // If collapse is shown by default (edit mode with existing user), stars should also be visible.
                toggleRequiredStars(true);
            } else {
                toggleRequiredStars(false);
            }
        });
    }
});
</script>