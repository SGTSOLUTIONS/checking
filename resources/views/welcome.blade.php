<!DOCTYPE html>
<html lang="ta">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coimbatore Corporation - Name Transfer (Property + Water) - Full Field Digital Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { background: #eef2f9; font-family: 'Segoe UI', Roboto, 'Noto Sans Tamil', sans-serif; }
        .form-container { max-width: 1400px; margin: 2rem auto; background: white; border-radius: 2rem; box-shadow: 0 20px 40px rgba(0,0,0,0.1); overflow: hidden; }
        .header-custom { background: #0a3a4f; color: white; padding: 1.5rem 2rem; border-bottom: 5px solid #f4a261; }
        .section-card { background: #fff; border-radius: 1.2rem; margin-bottom: 1.8rem; border: 1px solid #e0e9f0; box-shadow: 0 2px 6px rgba(0,0,0,0.03); }
        .section-title { background: #f4f9fe; padding: 0.9rem 1.5rem; border-bottom: 2px solid #cbdde9; font-weight: 700; font-size: 1.2rem; color: #1f5068; border-radius: 1.2rem 1.2rem 0 0; }
        .mandatory:after { content: " *"; color: #e03a3a; font-weight: bold; }
        .form-label { font-weight: 600; font-size: 0.85rem; margin-bottom: 0.25rem; color: #1e4663; }
        .btn-section { border-radius: 40px; padding: 6px 18px; font-weight: 500; margin-top: 12px; background: #f0f4f9; border: 1px solid #cbdde9; }
        .btn-section i { margin-right: 6px; }
        .btn-section:hover { background: #e2eaf1; transform: translateY(-1px); }
        .footer-note { font-size: 0.7rem; text-align: center; border-top: 1px solid #dce5ec; padding: 1rem; margin-top: 1rem; color: #5f7d9c; }
        .badge-custom { background: #ffedd5; color: #b45309; padding: 5px 12px; border-radius: 30px; font-size: 0.7rem; }
        @media (max-width: 768px) { .section-title { font-size: 1rem; } }
    </style>
</head>
<body>
<div class="form-container">
    <div class="header-custom">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-tint me-2"></i> Coimbatore Corporation</h2>
                <p class="mb-0">Property Tax + Water Connection Name Transfer – Complete Digital Form (As per All Fields)</p>
                <small><i class="fas fa-gavel"></i> Coimbatore City Municipal Corporation Act, 1981 & Water By-laws</small>
            </div>
            <div><span class="badge-custom"><i class="fas fa-stamp"></i> நீதிமன்ற கட்டணம் ₹1</span> <span class="badge bg-light text-dark ms-2">Form CMC/NT/ALL-FIELDS</span></div>
        </div>
    </div>
    <div class="p-4 p-xl-5">
        <form id="masterForm">
            <!-- SECTION A: METADATA + OFFICE REF (Image 1 top) -->
            <div class="section-card">
                <div class="section-title"><i class="fas fa-file-alt me-2 text-warning"></i> A. பதிவு & அலுவலக குறிப்பு (Registration & Office Reference)</div>
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-md-3"><label class="form-label mandatory">தேசப்பு எண் / கோப்பு எண் (File No.)</label><input type="text" class="form-control" id="fileNo" placeholder="e.g., CMC/NT/2026/123"></div>
                        <div class="col-md-3"><label class="form-label mandatory">C.F. No. / படிவ எண்</label><input type="text" class="form-control" id="cfNo" value="037905" placeholder="037905"></div>
                        <div class="col-md-3"><label class="form-label">விண்ணப்ப தேதி (Application Date)</label><input type="date" class="form-control" id="appDate"></div>
                        <div class="col-md-3"><label class="form-label">அனுவக குறிப்பு (Department Reference)</label><input type="text" class="form-control" id="deptRef" placeholder="Dept ref"></div>
                        <div class="col-md-4"><label class="form-label mandatory">குறிப்பு இணைப்பு எண் (Ref Attachment No.)</label><input type="text" class="form-control" id="refAttachNo"></div>
                        <div class="col-md-4"><label class="form-label mandatory">துறையோடு உறவுமையான பெயர் (Dept Related Name)</label><input type="text" class="form-control" id="deptRelatedName"></div>
                        <div class="col-md-4"><label class="form-label">சாட்சி கூறிய இளைஞரின் பெயர் / திருமதி (Witness Name)</label><input type="text" class="form-control" id="witnessName" placeholder="Witness name"></div>
                    </div>
                </div>
            </div>

            <!-- SECTION B: PROPERTY IDENTIFICATION + ASSESSMENT (GIS mapping core) -->
            <div class="section-card">
                <div class="section-title"><i class="fas fa-map-marker-alt me-2 text-warning"></i> B. சொத்து அடையாளம் (Property Identification - GIS Key)</div>
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label mandatory">மதிப்பீட்டு எண் / Assessment No. (🔥 GIS Link)</label><input type="text" class="form-control" id="assessmentNo" placeholder="Property Tax Assessment No."></div>
                        <div class="col-md-4"><label class="form-label mandatory">GIS ID / சொத்து அடையாள எண்</label><input type="text" class="form-control" id="gisId" placeholder="Polygon ID / GIS Key"></div>
                        <div class="col-md-4"><label class="form-label mandatory">கதவு எண் (Door No.)</label><input type="text" class="form-control" id="doorNo"></div>
                        <div class="col-md-3"><label class="form-label mandatory">வார்டு எண் (Ward No.)</label><input type="text" class="form-control" id="wardNo"></div>
                        <div class="col-md-3"><label class="form-label mandatory">தெரு பெயர் (Street)</label><input type="text" class="form-control" id="streetName"></div>
                        <div class="col-md-3"><label class="form-label">மண்டலம் (Zone)</label><input type="text" class="form-control" id="zone"></div>
                        <div class="col-md-3"><label class="form-label">பகுதி / ஊர் (Locality)</label><input type="text" class="form-control" id="locality"></div>
                    </div>
                </div>
            </div>

            <!-- SECTION C: OLD & NEW OWNER + RELATIONSHIP -->
            <div class="section-card">
                <div class="section-title"><i class="fas fa-users me-2 text-warning"></i> C. உரிமையாளர் விவரம் (Old Owner & New Owner)</div>
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label mandatory">தற்போதைய உரிமையாளர் பெயர் (Current Owner)</label><input type="text" class="form-control" id="currentOwner"></div>
                        <div class="col-md-6"><label class="form-label mandatory">புதிய உரிமையாளர் / விண்ணப்பதாரர் (New Owner)</label><input type="text" class="form-control" id="newOwner"></div>
                        <div class="col-md-4"><label class="form-label">தந்தை / கணவர் பெயர் (Father/Husband)</label><input type="text" class="form-control" id="fatherName"></div>
                        <div class="col-md-4"><label class="form-label">இணைப்புள்ள முகவரி (Address with connection)</label><input type="text" class="form-control" id="connAddress"></div>
                        <div class="col-md-4"><label class="form-label mandatory">உறவு முறை (Relationship)</label><select class="form-select" id="relationshipType"><option>விற்பனை (Sale)</option><option>பரம்பரை (Inheritance)</option><option>தானம் (Gift)</option><option>நீதிமன்ற உத்தரவு</option></select></div>
                    </div>
                </div>
            </div>

            <!-- SECTION D: TRANSFER DETAILS + DOCUMENT -->
            <div class="section-card">
                <div class="section-title"><i class="fas fa-file-signature me-2 text-warning"></i> D. மாற்றம் & பத்திரம் (Transfer Basis & Documents)</div>
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label mandatory">மாற்றம் கோரப்படும் காரணம் / சான்று</label><input type="text" class="form-control" id="transferReason" placeholder="Sale deed / Will / Gift"></div>
                        <div class="col-md-4"><label class="form-label">மாற்றம் கோருவதற்கான சான்று விளக்கம்</label><input type="text" class="form-control" id="transferProof"></div>
                        <div class="col-md-4"><label class="form-label">விற்பனை தொகை / சொத்து மதிப்பு</label><input type="text" class="form-control" id="saleAmount" placeholder="₹"></div>
                        <div class="col-md-4"><label class="form-label">பெயருக்கு மாதமும் பெயரளவும் (Month & Extent)</label><input type="text" class="form-control" id="monthExtent"></div>
                        <div class="col-md-4"><label class="form-label">பத்திர எண் (Document No.)</label><input type="text" class="form-control" id="docNumber"></div>
                        <div class="col-md-4"><label class="form-label">பத்திர தேதி</label><input type="date" class="form-control" id="docDate"></div>
                        <div class="col-md-6"><label class="form-label">பதிவாளர் அலுவலகம் (Sub-Registrar Office)</label><input type="text" class="form-control" id="registrarOffice"></div>
                        <div class="col-md-6"><label class="form-label">பதிவு செய்யப்பட்டதா? (Is Registered)</label><select class="form-select" id="isRegistered"><option>ஆம்</option><option>இல்லை</option></select></div>
                    </div>
                </div>
            </div>

            <!-- SECTION E: PROPERTY TAX NAME TRANSFER ORDER (As per image fields) -->
            <div class="section-card">
                <div class="section-title"><i class="fas fa-file-invoice-dollar me-2 text-warning"></i> E. செத்துவரி பெயர் மாற்ற உத்தரவு (Property Tax Transfer Order)</div>
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">அ) செத்துவி பெயர் மாற்றம் உத்திரவின் நகல் இணைக்கப்பட்டுள்ளதா?</label><select class="form-select" id="taxOrderCopyAttached"><option>ஆம்</option><option>இல்லை</option></select></div>
                        <div class="col-md-6"><label class="form-label">ஆ) அசல் உத்திரவுடன் நகல் சரிபார்க்கப்பட்டுள்ளதா?</label><select class="form-select" id="certifiedWithOriginal"><option>ஆம்</option><option>இல்லை</option></select></div>
                        <div class="col-md-4"><label class="form-label">இ) உத்தரவு எண் மற்றும் தேதி</label><input type="text" class="form-control" id="orderNoDate" placeholder="Order No. & Date"></div>
                        <div class="col-md-8"><label class="form-label mandatory">உத்தரவு இல்லையெனில் எந்த அடிப்படையில் பெயர் மாற்றம் கோரப்பட்டுள்ளது?</label><input type="text" class="form-control" id="basisWithoutOrder" placeholder="Sale deed / Will / Succession / Affidavit"></div>
                    </div>
                </div>
            </div>

            <!-- SECTION F: WATER CONNECTION SPECIFIC DETAILS (Assessment, Tariff) -->
            <div class="section-card">
                <div class="section-title"><i class="fas fa-hand-holding-water me-2 text-warning"></i> F. நீர் இணைப்பு விபரம் (Water Connection Details)</div>
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label mandatory">குழாய் இணைப்பு எண் (Water Connection No.)</label><input type="text" class="form-control" id="waterConnNo"></div>
                        <div class="col-md-4"><label class="form-label mandatory">நீர் இணைப்பு வகை (Usage Type)</label><select class="form-select" id="usageType"><option>குடியிருப்பு (Domestic)</option><option>குடியிருப்பு அல்லாத (Non-Domestic)</option></select></div>
                        <div class="col-md-4"><label class="form-label">தற்போதைய இணைப்பு பெயர் (Current Connection Name)</label><input type="text" class="form-control" id="currentConnName"></div>
                        <div class="col-md-4"><label class="form-label">மாற்ற வேண்டிய பெயர் (New Connection Name)</label><input type="text" class="form-control" id="newConnName"></div>
                    </div>
                </div>
            </div>

            <!-- SECTION G: WATER CHARGES & ARREARS (Meter reading) -->
            <div class="section-card">
                <div class="section-title"><i class="fas fa-charging-station me-2 text-warning"></i> G. நீர் கட்டண நிலுவை (Water Charges / Arrears)</div>
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label mandatory">7. அ) நீர்மானி கணக்கெடுப்பு அட்டையில் கட்டணம் நிலுவையின்றி செலுத்தப்பட்டுள்ளதா?</label><select class="form-select" id="meterClearStatus"><option>ஆம், முழுமையாக செலுத்தப்பட்டது</option><option>இல்லை, நிலுவை உள்ளது</option></select></div>
                        <div class="col-md-6"><label class="form-label">ஆ) நீர்மானி அட்டை வழங்குவதற்கு முந்தைய கால கட்டணம் நிலுவையின்றி செலுத்தப்பட்டுள்ளதா?</label><select class="form-select" id="preMeterClear"><option>ஆம்</option><option>இல்லை, விபரம் தருக</option></select></div>
                        <div class="col-12"><label class="form-label">நிலுவை விவரம் / எடுக்கப்பட்ட நடவடிக்கை</label><textarea class="form-control" rows="2" id="arrearsDetail" placeholder="Pending amount, action taken"></textarea></div>
                        <div class="col-12"><div class="form-check"><input class="form-check-input" type="checkbox" id="noDuesConfirm"> <label class="form-check-label">விண்ணப்ப தேதிக்கு முன் அனைத்து கட்டணமும் நிலுவையின்றி செலுத்தப்பட்டுள்ளது என உறுதி.</label></div></div>
                    </div>
                </div>
            </div>

            <!-- SECTION H: REGISTER ENTRY & TAX YEAR ARREARS -->
            <div class="section-card">
                <div class="section-title"><i class="fas fa-book-open me-2 text-warning"></i> H. குழாய் இணைப்பு பதிவேடு & வரி நிலுவை</div>
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">பெயர் மாற்றம் பதிவேட்டில் பதியப்பட்டுள்ளதா?</label><select class="form-select" id="registerEntryStatus"><option>ஆம்</option><option>இல்லை</option></select></div>
                        <div class="col-md-6"><label class="form-label">பதிவேடு எண் (Register No.)</label><input type="text" class="form-control" id="registerNumber"></div>
                        <div class="col-md-6"><label class="form-label">வரி நிலுவை (Tax status upto current year)</label><select class="form-select" id="taxYearStatus"><option>செலுத்தப்பட்டது (Paid)</option><option>நிலுவை உள்ளது (Pending)</option></select></div>
                        <div class="col-md-6"><label class="form-label">கடைசியாக வரி செலுத்திய தேதி</label><input type="date" class="form-control" id="lastTaxPaidDate"></div>
                    </div>
                </div>
            </div>

            <!-- SECTION I: LEGAL / DISPUTE + INSPECTOR VERIFICATION -->
            <div class="section-card">
                <div class="section-title"><i class="fas fa-gavel me-2 text-warning"></i> I. சொத்து உரிமம், வழக்கு & ஆய்வாளர் (Legal Dispute / Inspector)</div>
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">சொத்தில் ஏதேனும் வழக்கு நிலுவையில் உள்ளதா?</label><select class="form-select" id="disputeStatus"><option>இல்லை</option><option>ஆம் (விபரம் கீழே)</option></select></div>
                        <div class="col-md-6"><label class="form-label">வழக்கு விபரம் (if any)</label><input type="text" class="form-control" id="disputeDetails"></div>
                        <div class="col-md-4"><label class="form-label">உரிமம் ஆய்வாளர் பெயர்</label><input type="text" class="form-control" id="inspectorName"></div>
                        <div class="col-md-4"><label class="form-label">ஆய்வு நிலை (Inspection Status)</label><select class="form-select" id="inspectionStatus"><option>நிலுவை (Pending)</option><option>முடிவுற்றது (Completed)</option></select></div>
                        <div class="col-md-4"><label class="form-label">ஒப்புதல் பெற்ற தேதி (Approval Date)</label><input type="date" class="form-control" id="approvalDate"></div>
                    </div>
                </div>
            </div>

            <!-- SECTION J: AFFIDAVIT & DECLARATION + SIGNATURE -->
            <div class="section-card">
                <div class="section-title"><i class="fas fa-file-signature me-2 text-warning"></i> J. உறுதிமொழி & ஒப்புதல் (Affidavit + Declaration)</div>
                <div class="p-4">
                    <div class="bg-light p-3 rounded-3 border mb-3">
                        <p><i class="fas fa-check-circle text-success"></i> 1981-ம் ஆண்டின் கோயம்புத்தூர் மாநகராட்சி சட்டம் & நீர் வழங்கல் விதிகளுக்கு கட்டுப்படுவேன். நீரை தவறாக பயன்படுத்தவும் மாட்டேன்; பிறருக்கு வழங்கமாட்டேன். தரப்பட்ட விபரங்கள் அனைத்தும் உண்மை.</p>
                        <div class="form-check"><input class="form-check-input" type="checkbox" id="affidavitAccept"> <label class="form-check-label fw-bold">மேற்படி உறுதிமொழியை ஏற்று ஒப்புக்கொள்கிறேன்.</label></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><label class="form-label">இடம் (Place)</label><input type="text" class="form-control" id="place" value="Coimbatore"></div>
                        <div class="col-md-4"><label class="form-label">தேதி (Date)</label><input type="date" class="form-control" id="declarationDate"></div>
                        <div class="col-md-4"><label class="form-label">விண்ணப்பதாரர் கையொப்பம் (Signature)</label><input type="text" class="form-control" id="signatureName" placeholder="Applicant full name"></div>
                    </div>
                </div>
            </div>

            <!-- SECTION K: OFFICE USE ONLY REMARKS + PROCESSING FEE -->
            <div class="section-card">
                <div class="section-title"><i class="fas fa-stamp me-2 text-warning"></i> K. அலுவலக பயன்பாட்டுக்கு மட்டும் (Office Use Only)</div>
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label">Inward No. / உள்வரும் எண்</label><input type="text" class="form-control" id="inwardNo"></div>
                        <div class="col-md-4"><label class="form-label">செயலாக்க கட்டணம் (Processing Fee)</label><input type="text" class="form-control" id="processingFee" placeholder="₹"></div>
                        <div class="col-md-4"><label class="form-label">அபராத கட்டணம் (Penalty Fee)</label><input type="text" class="form-control" id="penaltyFee"></div>
                        <div class="col-md-6"><label class="form-label">எழுத்தர் / கண்காணிப்பாளர் கையொப்பம்</label><input type="text" class="form-control" id="clerkSign" placeholder="Clerk/Supervisor"></div>
                        <div class="col-md-6"><label class="form-label">ஆணையாளருக்காக (For Commissioner)</label><input type="text" class="form-control" id="commissionerSign"></div>
                        <div class="col-12"><label class="form-label">அலுவலக குறிப்பு: மேல்நடவடிக்கை அவசியமில்லை / 3 ஆண்டு முடிவாக முடிக்கலாம்</label><textarea class="form-control" rows="2" id="officeRemark" placeholder="No further action / closed after 3 years"></textarea></div>
                    </div>
                </div>
            </div>

            <!-- ACTION BUTTONS : Different Buttons to open DIFFERENT MODALS (section-wise) -->
            <div class="d-flex flex-wrap gap-3 mt-4 mb-3 justify-content-center">
                <button type="button" class="btn btn-outline-primary btn-section" data-section-modal="modalProperty"><i class="fas fa-home"></i> A-C: சொத்து & உரிமையாளர்</button>
                <button type="button" class="btn btn-outline-primary btn-section" data-section-modal="modalTaxWater"><i class="fas fa-file-invoice"></i> D-G: மாற்று & நீர்மானி</button>
                <button type="button" class="btn btn-outline-primary btn-section" data-section-modal="modalLegalAffidavit"><i class="fas fa-gavel"></i> H-J: பதிவேடு & உறுதிமொழி</button>
                <button type="button" class="btn btn-outline-primary btn-section" data-section-modal="modalOfficeOnly"><i class="fas fa-building"></i> K: அலுவலக பயன்பாடு</button>
                <button type="button" class="btn btn-success btn-section" id="viewAllModalBtn"><i class="fas fa-eye"></i> முழு விண்ணப்பத்தையும் காண்க (All fields)</button>
            </div>
        </form>
        <footer class="footer-note"><i class="fas fa-tint"></i> கோயம்புத்தூர் மாநகராட்சி - இணைப்பு பெயர் மாற்றம் | அனைத்து புலங்களும் GIS & வரி அமைப்புக்கு ஏற்றவாறு | C.F.No.037905</footer>
    </div>
</div>

<!-- MODALS (4 section modals + one all-in-one) -->
<div id="modalProperty" class="modal fade" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header bg-dark text-white"><h5><i class="fas fa-home"></i> சொத்து & உரிமையாளர் (Sections A-C)</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body" id="propModalBody"></div><div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">மூடு</button><button class="btn btn-primary printModalBtn" data-print-content="propModalBody">அச்சிடு</button></div></div></div></div>
<div id="modalTaxWater" class="modal fade" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header bg-dark text-white"><h5><i class="fas fa-file-invoice-dollar"></i> மாற்று & நீர்மானி (D-G)</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body" id="taxWaterModalBody"></div><div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">மூடு</button><button class="btn btn-primary printModalBtn" data-print-content="taxWaterModalBody">அச்சிடு</button></div></div></div></div>
<div id="modalLegalAffidavit" class="modal fade" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header bg-dark text-white"><h5><i class="fas fa-gavel"></i> பதிவேடு & உறுதிமொழி (H-J)</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body" id="legalModalBody"></div><div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">மூடு</button><button class="btn btn-primary printModalBtn" data-print-content="legalModalBody">அச்சிடு</button></div></div></div></div>
<div id="modalOfficeOnly" class="modal fade" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header bg-dark text-white"><h5><i class="fas fa-stamp"></i> அலுவலக பயன்பாடு (Section K)</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body" id="officeModalBody"></div><div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">மூடு</button><button class="btn btn-primary printModalBtn" data-print-content="officeModalBody">அச்சிடு</button></div></div></div></div>
<div id="allFieldsModal" class="modal fade" tabindex="-1"><div class="modal-dialog modal-xl modal-dialog-scrollable"><div class="modal-content"><div class="modal-header bg-dark text-white"><h5><i class="fas fa-database"></i> முழுமையான விண்ணப்பம் - Coimbatore Corporation (All Fields)</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body" id="allModalBody"></div><div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">மூடு</button><button class="btn btn-primary" id="printAllFieldsBtn">PDF / அச்சிடு</button></div></div></div></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function getField(id) { return document.getElementById(id)?.value || ''; }
    function getCheckText(id, trueText="✔️ உறுதி செய்யப்பட்டது", falseText="❌ உறுதி செய்யவில்லை") { return document.getElementById(id)?.checked ? trueText : falseText; }

    function getPropHtml() { return `<div class="border p-3 rounded"><strong>📂 File No:</strong> ${getField('fileNo')}<br><strong>Assessment No (GIS):</strong> ${getField('assessmentNo')}<br><strong>GIS ID:</strong> ${getField('gisId')}<br><strong>Door/Ward/Street:</strong> ${getField('doorNo')}, ${getField('wardNo')}, ${getField('streetName')}<br><strong>Current Owner:</strong> ${getField('currentOwner')}<br><strong>New Owner:</strong> ${getField('newOwner')}<br><strong>Relationship:</strong> ${getField('relationshipType')}<br><strong>Witness Name:</strong> ${getField('witnessName')}</div>`; }
    function getTaxWaterHtml() { return `<div class="border p-3 rounded"><strong>Transfer Reason:</strong> ${getField('transferReason')}<br><strong>Document No/Date:</strong> ${getField('docNumber')} / ${getField('docDate')}<br><strong>Tax Order Copy:</strong> ${getField('taxOrderCopyAttached')}<br><strong>Basis Without Order:</strong> ${getField('basisWithoutOrder')}<br><strong>Water Connection No:</strong> ${getField('waterConnNo')}<br><strong>Meter Arrears Status:</strong> ${getField('meterClearStatus')}<br><strong>Arrears details:</strong> ${getField('arrearsDetail')}<br><strong>No dues confirmed:</strong> ${getCheckText('noDuesConfirm')}</div>`; }
    function getLegalAffHtml() { return `<div class="border p-3 rounded"><strong>Register Entry:</strong> ${getField('registerEntryStatus')} | Reg No: ${getField('registerNumber')}<br><strong>Tax Year Status:</strong> ${getField('taxYearStatus')}<br><strong>Dispute:</strong> ${getField('disputeStatus')} - ${getField('disputeDetails')}<br><strong>Inspector:</strong> ${getField('inspectorName')}<br><strong>Approval Date:</strong> ${getField('approvalDate')}<br><strong>Affidavit Accept:</strong> ${getCheckText('affidavitAccept')}<br><strong>Place/Sign:</strong> ${getField('place')}, ${getField('signatureName')}</div>`; }
    function getOfficeHtml() { return `<div class="border p-3 rounded"><strong>Inward No:</strong> ${getField('inwardNo')}<br><strong>Processing Fee:</strong> ${getField('processingFee')}<br><strong>Clerk Sign:</strong> ${getField('clerkSign')}<br><strong>Commissioner Sign:</strong> ${getField('commissionerSign')}<br><strong>Office Remark:</strong> ${getField('officeRemark')}</div>`; }
    function getAllHtml() { return `<div class="row"><div class="col-md-6">${getPropHtml()}</div><div class="col-md-6">${getTaxWaterHtml()}</div><div class="col-12 mt-2">${getLegalAffHtml()}</div><div class="col-12 mt-2">${getOfficeHtml()}</div><div class="alert alert-info mt-3"><strong>✅ GIS / Tax Integration Ready:</strong> Assessment No: ${getField('assessmentNo')} | GIS ID: ${getField('gisId')} | Ward: ${getField('wardNo')}</div></div>`; }

    document.querySelectorAll('button[data-section-modal]').forEach(btn => {
        btn.addEventListener('click', () => {
            let target = btn.getAttribute('data-section-modal');
            let modalId, contentBody, htmlContent;
            if (target === 'modalProperty') { modalId = 'modalProperty'; contentBody = 'propModalBody'; htmlContent = getPropHtml(); }
            else if (target === 'modalTaxWater') { modalId = 'modalTaxWater'; contentBody = 'taxWaterModalBody'; htmlContent = getTaxWaterHtml(); }
            else if (target === 'modalLegalAffidavit') { modalId = 'modalLegalAffidavit'; contentBody = 'legalModalBody'; htmlContent = getLegalAffHtml(); }
            else if (target === 'modalOfficeOnly') { modalId = 'modalOfficeOnly'; contentBody = 'officeModalBody'; htmlContent = getOfficeHtml(); }
            document.getElementById(contentBody).innerHTML = htmlContent + `<div class="mt-3 small text-muted"><i class="fas fa-stamp"></i> Coimbatore Corporation - மாற்ற விண்ணப்பம்</div>`;
            new bootstrap.Modal(document.getElementById(modalId)).show();
        });
    });
    document.getElementById('viewAllModalBtn').addEventListener('click', () => { document.getElementById('allModalBody').innerHTML = getAllHtml(); new bootstrap.Modal(document.getElementById('allFieldsModal')).show(); });
    document.getElementById('printAllFieldsBtn').addEventListener('click', () => { let win = window.open('', '_blank'); win.document.write(`<html><head><title>Coimbatore Full Form</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"></head><body><div class="container mt-4">${getAllHtml()}</div></body></html>`); win.document.close(); win.print(); });
    document.querySelectorAll('.printModalBtn').forEach(btn => { btn.addEventListener('click', (e) => { let contentId = btn.getAttribute('data-print-content'); let content = document.getElementById(contentId).innerHTML; let win = window.open('', '_blank'); win.document.write(`<html><head><title>Coimbatore Section</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"></head><body><div class="container mt-4">${content}</div></body></html>`); win.document.close(); win.print(); }); });
    if(!document.getElementById('appDate').value) document.getElementById('appDate').valueAsDate = new Date();
    if(!document.getElementById('declarationDate').value) document.getElementById('declarationDate').valueAsDate = new Date();
</script>
</body>
</html>
