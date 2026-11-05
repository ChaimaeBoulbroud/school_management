{{-- resources/views/admin/eleves/_form_scripts.blade.php --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // --- Guardian Option Toggle & AJAX ---
    const guardianOptionRadios = document.querySelectorAll('.guardian-option-radio');
    const existingGuardianFieldsCollapseEl = document.getElementById('existing_guardian_fields_collapse');
    const newGuardianFieldsCollapseEl = document.getElementById('new_guardian_fields_collapse');
    const bsExistingGuardianCollapse = existingGuardianFieldsCollapseEl ? new bootstrap.Collapse(existingGuardianFieldsCollapseEl, { toggle: false }) : null;
    const bsNewGuardianCollapse = newGuardianFieldsCollapseEl ? new bootstrap.Collapse(newGuardianFieldsCollapseEl, { toggle: false }) : null;

    const saveNewGuardianBtn = document.getElementById('save_new_guardian_ajax_btn');
    const parentIdSelectDropdown = document.getElementById('parent_id_select'); // The <select>
    const selectedParentIdHiddenInput = document.getElementById('selected_parent_id_for_eleve'); // The hidden input <input name="parent_id">

    const guardianPrenomAjaxInput = document.getElementById('guardian_prenom_ajax');
    const guardianNomAjaxInput = document.getElementById('guardian_nom_ajax');
    const guardianCINAjaxInput = document.getElementById('guardian_CIN_ajax');
    const guardianTelephoneAjaxInput = document.getElementById('guardian_telephone_ajax');
    const ajaxGuardianErrorsDiv = document.getElementById('ajax_guardian_errors');
    const guardianAjaxLoader = document.getElementById('guardian_ajax_loader');

    function toggleGuardianSectionsUI() {
        const isNewChecked = document.getElementById('guardian_new_radio').checked;
        if (isNewChecked) {
            if(bsExistingGuardianCollapse) bsExistingGuardianCollapse.hide();
            if(bsNewGuardianCollapse) bsNewGuardianCollapse.show();
            if(parentIdSelectDropdown) parentIdSelectDropdown.disabled = true;
            // Enable new guardian fields
            if(guardianPrenomAjaxInput) guardianPrenomAjaxInput.disabled = false;
            if(guardianNomAjaxInput) guardianNomAjaxInput.disabled = false;
            if(guardianCINAjaxInput) guardianCINAjaxInput.disabled = false;
            if(guardianTelephoneAjaxInput) guardianTelephoneAjaxInput.disabled = false;

            selectedParentIdHiddenInput.value = ''; // Clear selected existing parent when switching to new
        } else { // 'existing' radio is checked
            if(bsNewGuardianCollapse) bsNewGuardianCollapse.hide();
            if(bsExistingGuardianCollapse) bsExistingGuardianCollapse.show();
            if(parentIdSelectDropdown) parentIdSelectDropdown.disabled = false;
            // Disable new guardian fields
            if(guardianPrenomAjaxInput) guardianPrenomAjaxInput.disabled = true;
            if(guardianNomAjaxInput) guardianNomAjaxInput.disabled = true;
            if(guardianCINAjaxInput) guardianCINAjaxInput.disabled = true;
            if(guardianTelephoneAjaxInput) guardianTelephoneAjaxInput.disabled = true;
            // Set hidden input value from dropdown
            if(parentIdSelectDropdown) selectedParentIdHiddenInput.value = parentIdSelectDropdown.value;
        }
    }

    guardianOptionRadios.forEach(radio => {
        radio.addEventListener('change', toggleGuardianSectionsUI);
    });
    // Initial UI setup based on which radio is checked by default (due to old() or model data)
    if (document.querySelector('.guardian-option-radio:checked')) {
        toggleGuardianSectionsUI();
    }


    if (parentIdSelectDropdown) {
        parentIdSelectDropdown.addEventListener('change', function() {
            selectedParentIdHiddenInput.value = this.value;
        });
        // Ensure hidden input is synced on load if a value is already selected in dropdown
        if(parentIdSelectDropdown.value) {
            selectedParentIdHiddenInput.value = parentIdSelectDropdown.value;
        }
    }


    if (saveNewGuardianBtn) {
        saveNewGuardianBtn.addEventListener('click', function() {
            guardianAjaxLoader.classList.remove('d-none');
            saveNewGuardianBtn.disabled = true;
            ajaxGuardianErrorsDiv.classList.add('d-none');
            ajaxGuardianErrorsDiv.innerHTML = '';
            // Clear previous inline errors for AJAX guardian fields
            ['guardian_prenom_ajax_error', 'guardian_nom_ajax_error', 'guardian_CIN_ajax_error', 'guardian_telephone_ajax_error'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.textContent = '';
            });

            const guardianData = {
                guardian_prenom: guardianPrenomAjaxInput.value,
                guardian_nom: guardianNomAjaxInput.value,
                guardian_CIN: guardianCINAjaxInput.value,
                guardian_telephone: guardianTelephoneAjaxInput.value,
            };

            fetch("{{ route('admin.ajax.guardians.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(guardianData)
            })
            .then(response => {
                // Regardless of ok status, try to parse JSON, as validation errors also come as JSON
                return response.json().then(data => ({ status: response.status, body: data }));
            })
            .then(({ status, body }) => {
                guardianAjaxLoader.classList.add('d-none');
                saveNewGuardianBtn.disabled = false;

                if (status === 200 && body.success && body.guardian) { // Assuming 200 for success
                    const newOption = new Option(body.guardian.full_name, body.guardian.id, true, true);
                    parentIdSelectDropdown.add(newOption);
                    selectedParentIdHiddenInput.value = body.guardian.id;

                    document.getElementById('guardian_existing_radio').checked = true;
                    toggleGuardianSectionsUI(); // This will hide new fields and show existing

                    guardianPrenomAjaxInput.value = '';
                    guardianNomAjaxInput.value = '';
                    guardianCINAjaxInput.value = '';
                    guardianTelephoneAjaxInput.value = '';

                    ajaxGuardianErrorsDiv.classList.remove('alert-danger', 'd-none');
                    ajaxGuardianErrorsDiv.classList.add('alert-success');
                    ajaxGuardianErrorsDiv.textContent = body.message || 'Parent/Gardien ajouté et sélectionné.';
                } else if (status === 422 && body.errors) { // Validation errors
                    ajaxGuardianErrorsDiv.classList.remove('alert-success', 'd-none');
                    ajaxGuardianErrorsDiv.classList.add('alert-danger');
                    let errorHtml = '<strong>Erreurs de validation pour le parent/gardien:</strong><ul>';
                    for (const fieldKey in body.errors) {
                        // fieldKey for AJAX inputs might be 'guardian_nom', 'guardian_prenom' etc.
                        // or just 'nom', 'prenom' depending on your GuardianController@ajaxStore validation keys
                        let inputFieldIdPart = fieldKey;
                        // Assuming GuardianController@ajaxStore uses 'guardian_nom' etc. as keys in $request->all() for Validator::make
                        // If it uses just 'nom', 'prenom', you'd need to map them here or in controller response.
                        // For simplicity, let's assume the error keys from ajaxStore match the input field names (guardian_nom, etc.)
                        const inlineErrorEl = document.getElementById(fieldKey + '_ajax_error'); // e.g. guardian_nom_ajax_error
                        body.errors[fieldKey].forEach(message => {
                            errorHtml += `<li>${message}</li>`;
                            if(inlineErrorEl) inlineErrorEl.textContent = message;
                        });
                    }
                    errorHtml += '</ul>';
                    ajaxGuardianErrorsDiv.innerHTML = errorHtml;
                } else { // Other errors (e.g., 500 server error)
                    ajaxGuardianErrorsDiv.classList.remove('alert-success', 'd-none');
                    ajaxGuardianErrorsDiv.classList.add('alert-danger');
                    ajaxGuardianErrorsDiv.textContent = body.message || 'Une erreur est survenue lors de l\'enregistrement du parent/gardien.';
                }
            })
            .catch(error => { // Network error or JSON parsing error from a non-JSON response
                guardianAjaxLoader.classList.add('d-none');
                saveNewGuardianBtn.disabled = false;
                ajaxGuardianErrorsDiv.classList.remove('alert-success', 'd-none');
                ajaxGuardianErrorsDiv.classList.add('alert-danger');
                ajaxGuardianErrorsDiv.textContent = 'Une erreur de communication est survenue. Vérifiez la console.';
                console.error('AJAX Guardian Save Error:', error);
            });
        });
    }


    // --- Niveau to Groupe Dynamic Dropdown ---
    const niveauSelect = document.getElementById('niveau_select');
    const groupeSelect = document.getElementById('groupe_id');

    let currentEleveGroupeId = "{{ old('groupe_id', $eleve->groupe_id ?? '') }}";
    let currentEleveNiveau = "{{ old('niveau_pour_groupe_display', ($eleve->groupe->niveau ?? ($eleve->exists && $eleve->groupe_id ? (\App\Models\Groupe::find($eleve->groupe_id)->niveau ?? '') : ''))) }}";

    function fetchGroupesForEleve(niveau, preselectGroupeId = null) {
        groupeSelect.innerHTML = '<option value="">Chargement...</option>';
        groupeSelect.disabled = true;

        if (!niveau) {
            groupeSelect.innerHTML = '<option value="">D\'abord choisir un niveau...</option>';
            return;
        }

        fetch(`{{ route('admin.eleves.getGroupesByNiveau') }}?niveau=${encodeURIComponent(niveau)}`)
            .then(response => {
                if (!response.ok) {
                    console.error('Network response was not ok for getGroupesByNiveau:', response);
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(groupes => {
                groupeSelect.innerHTML = '<option value="">Choisir un groupe...</option>';
                if (groupes.length > 0) {
                    groupes.forEach(groupe => {
                        const option = new Option(`${groupe.nom} (${groupe.niveau})`, groupe.id);
                        if (String(groupe.id) === String(preselectGroupeId)) {
                            option.selected = true;
                        }
                        groupeSelect.add(option);
                    });
                } else {
                    groupeSelect.innerHTML = '<option value="">Aucun groupe pour ce niveau</option>';
                }
                groupeSelect.disabled = false;
            })
            .catch(error => {
                console.error('Erreur lors de la récupération des groupes pour Eleve:', error);
                groupeSelect.innerHTML = '<option value="">Erreur de chargement des groupes</option>';
                groupeSelect.disabled = false;
            });
    }

    if (niveauSelect) {
        niveauSelect.addEventListener('change', function () {
            let preselectIdOnNiveauChange = null;
            // If the selected niveau in the dropdown matches the original/old niveau associated with the student's group
            if (this.value === currentEleveNiveau) {
                preselectIdOnNiveauChange = currentEleveGroupeId;
            }
            fetchGroupesForEleve(this.value, preselectIdOnNiveauChange);
        });

        // Initial load: if a niveau is already selected (from old() or edit data), fetch groupes
        if (niveauSelect.value) {
            fetchGroupesForEleve(niveauSelect.value, currentEleveGroupeId);
        } else {
            groupeSelect.innerHTML = '<option value="">D\'abord choisir un niveau...</option>';
            groupeSelect.disabled = true;
        }
    }
});
</script>