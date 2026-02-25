<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Job Application Form</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>



<body>
    <!-- Loader -->
<div id="loaderOverlay">
    <div class="loader"></div>
    <p>Submitting your application...</p>
</div>

<div class="banner text-center py-5">
    <h1>Job Application Form</h1>
    <p>Complete all steps to apply</p>
</div>

<div id="formPage" class="container mt-5">
    <div class="form-box mx-auto p-4 shadow rounded">

        <h3 id="stepTitle" class="text-center mb-4">Step 1: Basic Information</h3>

        <div class="progress mb-4">
            <div class="progress-bar" id="progressBar" style="width:33%;"></div>
        </div>

        <form id="jobForm" method="POST" action="submit.php" enctype="multipart/form-data" novalidate>

            <!-- STEP 1 -->
            <div class="step active">
                <h5>👤 Basic Information</h5>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Mobile Number *</label>
                        <input type="tel" name="mobile" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Date of Birth *</label>
                        <input type="date" name="dob" class="form-control" required>
                    </div>
                </div>

                <label class="form-label mt-3">Gender *</label>
                <div class="d-flex gap-4">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="gender" value="Male" required>
                        <label class="form-check-label">Male</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="gender" value="Female">
                        <label class="form-check-label">Female</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="gender" value="Other">
                        <label class="form-check-label">Other</label>
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label">Address *</label>
                    <textarea name="address" class="form-control" rows="3" required></textarea>
                </div>

                <div class="row g-3 mt-3">
                    <div class="col-md-6">
                        <label class="form-label">Nationality *</label>
                        <select name="nationality" class="form-select" required>
                            <option value="">Select</option>
                            <option>Indian</option>
                            <option>American</option>
                            <option>British</option>
                            <option>Other</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Willing to Relocate *</label>
                        <div class="d-flex gap-4 mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="relocate" value="Yes" required>
                                <label class="form-check-label">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="relocate" value="No">
                                <label class="form-check-label">No</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="button" class="btn btn-primary next-btn">Next →</button>
                </div>
            </div>

            <!-- STEP 2 -->
            <div class="step">
                <h5>🎓 Academic Information</h5>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Highest Qualification *</label>
                        <select name="qualification" class="form-select" required>
                            <option value="">Select</option>
                            <option>Bachelor's</option>
                            <option>Master's</option>
                            <option>PhD</option>
                            <option>Diploma</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Year of Graduation *</label>
                        <input type="number" name="graduation_year" class="form-control" min="1900" max="2030" required>
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label">Institution *</label>
                    <input type="text" name="institution" class="form-control" required>
                </div>

                <div class="mt-3">
                    <label class="form-label">CGPA / Percentage *</label>
                    <input type="text" name="cgpa" class="form-control" required>
                </div>

                <div class="mt-3">
                    <label class="form-label required">Technical Skills *</label>
                    <div class="row">
                        <div class="col-md-3 form-check">
                            <input class="form-check-input" type="checkbox" name="skills[]" value="Frontend">
                            <label class="form-check-label">Frontend</label>
                        </div>
                        <div class="col-md-3 form-check">
                            <input class="form-check-input" type="checkbox" name="skills[]" value="Backend">
                            <label class="form-check-label">Backend</label>
                        </div>
                        <div class="col-md-3 form-check">
                            <input class="form-check-input" type="checkbox" name="skills[]" value="Fullstack">
                            <label class="form-check-label">Fullstack</label>
                        </div>
                        <div class="col-md-3 form-check">
                            <input class="form-check-input" type="checkbox" name="skills[]" value="Testing">
                            <label class="form-check-label">Testing</label>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label">Certifications (Optional)</label>
                    <input type="text" name="certifications" class="form-control">
                </div>

                <div class="mt-3">
                    <label class="form-label">Certification Upload (Optional)</label>
                    <input type="file" name="certification_file" class="form-control" accept=".pdf,.doc,.docx">
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-secondary prev-btn">← Previous</button>
                    <button type="button" class="btn btn-primary next-btn">Next →</button>
                </div>
            </div>

            <!-- STEP 3 -->
            <div class="step">
                <h5>💼 Experience & Resume</h5>

                <div class="mb-3">
                    <label class="form-label">Total Work Experience *</label>
                    <select name="experience" class="form-select" required>
                        <option value="">Select</option>
                        <option>Fresher</option>
                        <option>1–3 Years</option>
                        <option>3–5 Years</option>
                        <option>5+ Years</option>
                    </select>
                </div>

                <label class="form-label">Current Employment Status *</label>
                <div class="d-flex gap-4 mb-3">
                    <input type="radio" name="employment_status" value="Employed" required> Employed
                    <input type="radio" name="employment_status" value="Unemployed"> Unemployed
                    <input type="radio" name="employment_status" value="Student"> Student
                </div>

                <div class="mb-3">
                    <label class="form-label">Previous Organization</label>
                    <input type="text" name="previous_org" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Job Title</label>
                    <input type="text" name="job_title" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Skills Summary</label>
                    <textarea name="skills_summary" class="form-control" rows="4"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Resume Upload *</label>
                    <input type="file" name="resume" class="form-control" required>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="declaration" required>
                    <label class="form-check-label">I declare all information provided is correct</label>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary prev-btn">← Previous</button>
                    <button type="submit" class="btn btn-success">Submit Application</button>
                </div>
            </div>

        </form>
    </div>
</div>

<script src="script.js"></script>
</body>
</html>
