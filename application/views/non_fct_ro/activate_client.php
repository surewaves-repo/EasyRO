<html>
<head>
    <script src="/surewaves_easy_ro/js/non_fct_ro_activate_client_script.js"></script>
   <link rel="stylesheet" href="/surewaves_easy_ro/css/non_fct_ro_activate_client.css">
</head>
<body>
<form  id="non_fct_ro_activate_client_form">
    <table class="table" id="non_fct_ro_activate_client_table" width="100%">
        <tr>
            <td width="20%">Select Client</td>
            <td width="5%"> : </td>
            <td width="25%">
                <select name="sel_client" class="form-control" id="sel_client">
                    <option selected disabled>Choose Client</option>
                    <?php foreach($client as $val){?>
                        <option value="<?php echo $val['id']?>"><?php echo $val['advertiser']?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <input type="submit" id="non_fct_ro_activate_client_submit_btn" class="submit btn btn-primary" value="Activate" />
            </td>
        <tr>
    </table>
</form>
</body>

</html>
