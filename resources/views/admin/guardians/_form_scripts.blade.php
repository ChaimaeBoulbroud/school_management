{{-- resources/views/admin/guardians/_form_scripts.blade.php --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const createUserCheckbox = document.getElementById('create_user_account');
    const userAccountFieldsCollapseEl = document.getElementById('user_account_fields_collapse');
    const emailRequiredStar = document.getElementById('email_required_star');
    const passwordRequiredStar = document.getElementById('password_required_star');

    // This check is for create mode where the checkbox exists
    if (createUserCheckbox && userAccountFieldsCollapseEl) {
        // Bootstrap Collapse instance (if using Bootstrap JS for collapse)
        const bsUserAccountCollapse = new bootstrap.Collapse(userAccountFieldsCollapseEl, {
             toggle: createUserCheckbox.checked // Initialize based on checkbox state
        });

        createUserCheckbox.addEventListener('change', function () {
            if (this.checked) {
                bsUserAccountCollapse.show();
                if(emailRequiredStar) emailRequiredStar.classList.remove('d-none');
                if(passwordRequiredStar) passwordRequiredStar.classList.remove('d-none');
            } else {
                bsUserAccountCollapse.hide();
                if(emailRequiredStar) emailRequiredStar.classList.add('d-none');
                if(passwordRequiredStar) passwordRequiredStar.classList.add('d-none');
            }
        });
    } else if (userAccountFieldsCollapseEl) {
        // This part handles the edit form if a user already exists,
        // ensuring the fields are shown by default if the collapse div has 'show'.
        // No checkbox to toggle in this scenario, fields are for updating.
        // The data-bs-toggle on the checkbox in the form handles create mode.
        // For edit mode where user exists, the fields are always "collapsible" but shown by default.
    }
});
</script>