document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('jobForm');
    const steps = document.querySelectorAll('.step');
    const progressBar = document.getElementById('progressBar');
    const stepTitle = document.getElementById('stepTitle');
    const loader = document.getElementById('loaderOverlay');

    const stepTitles = [
        "Step 1: Basic Information",
        "Step 2: Academic Information",
        "Step 3: Experience & Resume"
    ];

    let currentStep = 0;

    function goToStep(n) {
        currentStep = n;

        steps.forEach((step, i) =>
            step.classList.toggle('active', i === currentStep)
        );

        stepTitle.textContent = stepTitles[currentStep];
        progressBar.style.width = ((currentStep + 1) / steps.length * 100) + '%';

        document.querySelector('.form-box')
            .scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function showError(field, message) {
        field.classList.add('is-invalid');

        if (!field.nextElementSibling || !field.nextElementSibling.classList.contains('invalid-feedback')) {
            const error = document.createElement('div');
            error.className = 'invalid-feedback';
            error.textContent = message;
            field.insertAdjacentElement('afterend', error);
        }
    }

    function clearError(field) {
        field.classList.remove('is-invalid');
        if (field.nextElementSibling?.classList.contains('invalid-feedback')) {
            field.nextElementSibling.remove();
        }
    }

    function isStepValid() {
        const stepEl = steps[currentStep];
        let isValid = true;
        const validatedGroups = new Set();

        stepEl.querySelectorAll('.is-invalid').forEach(clearError);

        stepEl.querySelectorAll('[required]').forEach(field => {

            let valid = true;

            if (field.type === 'radio') {
                if (validatedGroups.has(field.name)) return;
                validatedGroups.add(field.name);

                if (!stepEl.querySelector(`input[name="${field.name}"]:checked`)) {
                    valid = false;
                    stepEl.querySelectorAll(`input[name="${field.name}"]`)
                        .forEach(r => showError(r, "Please select an option."));
                }
            }
            else if (field.type === 'checkbox' && field.name === 'skills[]') {
                if (validatedGroups.has(field.name)) return;
                validatedGroups.add(field.name);

                if (!stepEl.querySelector('input[name="skills[]"]:checked')) {
                    valid = false;
                    stepEl.querySelectorAll('input[name="skills[]"]')
                        .forEach(c => showError(c, "Select at least one skill."));
                }
            }
            else if (field.type === 'checkbox' && !field.checked) {
                valid = false;
                showError(field, "This field is required.");
            }
            else if (field.type === 'file' && !field.files.length) {
                valid = false;
                showError(field, "Please upload a file.");
            }
            else if (field.type === 'email') {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!field.value.trim()) {
                    valid = false;
                    showError(field, "Email is required.");
                } else if (!emailPattern.test(field.value.trim())) {
                    valid = false;
                    showError(field, "Enter a valid email.");
                }
            }
            else if (field.type === 'number') {
                if (!field.value.trim()) {
                    valid = false;
                    showError(field, "This field is required.");
                } else if (+field.value < field.min || +field.value > field.max) {
                    valid = false;
                    showError(field, `Enter a value between ${field.min} and ${field.max}.`);
                }
            }
            else if (!field.value.trim()) {
                valid = false;
                showError(field, "This field is required.");
            }

            if (!valid) isValid = false;
        });

        return isValid;
    }

    document.querySelectorAll('.next-btn').forEach(btn => {
        btn.addEventListener('click', () => {

            if (!isStepValid()) return;

            if (currentStep === steps.length - 1) {
                loader.classList.add('active');
                btn.disabled = true;

                setTimeout(() => {
                    form.submit();
                }, 300);
            } else {
                goToStep(currentStep + 1);
            }
        });
    });

    document.querySelectorAll('.prev-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            if (currentStep > 0) goToStep(currentStep - 1);
        });
    });

    document.querySelectorAll('input, select, textarea').forEach(el => {
        el.addEventListener('input', () => clearError(el));
        el.addEventListener('change', () => clearError(el));
    });

    goToStep(0);
});
