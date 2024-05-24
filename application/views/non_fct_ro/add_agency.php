<html>
<head>
    <script src="/surewaves_easy_ro/js/non_fct_ro_add_agency_script.js"></script>
    <link rel="stylesheet" href="/surewaves_easy_ro/css/non_fct_ro_add_agency.css">
</head>
<body>
<form  id="non_fct_ro_add_agency_form" >

    <table class="table" id="non_fct_ro_add_agency_table">
        <!----Agency Name--->
        <tr>
            <td width="20%">Agency Name<span class="Asterik"> *</span></td>
            <td width="5%"> :</td>
            <td width="25%">
                <input type="text" name="txt_agency_name" class="stylingwidth" id="txt_agency_name"/>
            </td>
        </tr>
        <!----Agency Billing Name--->
        <tr>
            <td width="20%">Agency Billing Name<span class="Asterik"> *</span></td>
            <td width="5%"> :</td>
            <td width="25%">
                <input type="text" name="txt_agency_billing_name" class="stylingwidth" id="txt_agency_billing_name"/>
            </td>
        </tr>
        <!---Agency Address---->
        <tr>
            <td width="20%">Agency Address <span class="Asterik"> *</span></td>
            <td width="5%"> :</td>
            <td width="25%">
                <input type="text" name="txt_agency_address" class="stylingwidth" id="txt_agency_address"/>
            </td>
        </tr>

        <!----Submit Button--->
        <tr>
            <td colspan="3">
                <input type="submit" class="submit btn btn-primary" id="submit_add_agency_btn" value="Add"/>
            </td>
        <tr>
    </table>
</form>
</body>
</html>
