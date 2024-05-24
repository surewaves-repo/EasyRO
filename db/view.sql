-- Create View For Network Remittance --

CREATE VIEW `Network_Remitance_Report_View` AS
    (select 
        `rnrr`.`customer_name` AS `customer_name`,
        `rnrr`.`internal_ro_number` AS `internal_ro_number`,
        (select 
                `ro_network_payment`.`payment_paid`
            from
                `ro_network_payment`
            where
                (`ro_network_payment`.`Network_ro_number` = `rnrr`.`network_ro_number`)
            order by `ro_network_payment`.`payment_paid` desc
            limit 1) AS `Network_RO_fully_paid`,
        (select 
                `ro_am_invoice_collection`.`chk_complete`
            from
                `ro_am_invoice_collection`
            where
                (`ro_am_invoice_collection`.`ext_ro` = `rnrr`.`customer_ro_number`)
            order by `ro_am_invoice_collection`.`chk_complete` desc
            limit 1) AS `Full_Payment_Received`,
        `rnrr`.`client_name` AS `client_name`,
        `rnrr`.`network_ro_number` AS `network_ro_number`,
        `rnrr`.`release_date` AS `release_date`,
        (select 
                sum(`ro_network_payment`.`amount_paid`)
            from
                `ro_network_payment`
            where
                (`ro_network_payment`.`Network_ro_number` = `rnrr`.`network_ro_number`)) AS `Network_RO_Paid_Amount`,
        ((`rnrr`.`gross_network_ro_amount` * (100 - `rnrr`.`customer_share`)) / 100) AS `SureWaves_Share_Amount`,
        (select 
                `sv_customer`.`billing_name`
            from
                `sv_customer`
            where
                (`sv_customer`.`customer_name` = `rnrr`.`customer_name`)) AS `Network_Billing_Name`,
        `rnrr`.`customer_ro_number` AS `customer_ro_number`,
        `rnrr`.`agency_name` AS `agency_name`,
        (select 
                `ro_am_external_ro`.`test_user_creation`
            from
                `ro_am_external_ro`
            where
                (`ro_am_external_ro`.`cust_ro` = `rnrr`.`customer_ro_number`)
            limit 1) AS `is_test_ro`,
        (select 
                `ro_am_external_ro`.`camp_start_date`
            from
                `ro_am_external_ro`
            where
                (`ro_am_external_ro`.`cust_ro` = `rnrr`.`customer_ro_number`)
            limit 1) AS `Activity_Start_Date`,
        (select 
                `ro_am_external_ro`.`camp_end_date`
            from
                `ro_am_external_ro`
            where
                (`ro_am_external_ro`.`cust_ro` = `rnrr`.`customer_ro_number`)
            limit 1) AS `Activity_End_Date`,
        1 AS `Activity_Run_Spot_Seconds`,
        1 AS `Activity_Run_Banner_Seconds`,
        (select 
                sum(`ro_am_invoice_collection`.`amnt_collected`)
            from
                `ro_am_invoice_collection`
            where
                (`ro_am_invoice_collection`.`ext_ro` = `rnrr`.`customer_ro_number`)) AS `Payment_Collected_Amount`,
        (select 
                `ro_am_external_ro`.`gross`
            from
                `ro_am_external_ro`
            where
                (`ro_am_external_ro`.`cust_ro` = `rnrr`.`customer_ro_number`)
            limit 1) AS `External_RO_Amount`,
        `rnrr`.`gross_network_ro_amount` AS `Network_RO_Amount`,
        1.1236 AS `Service_Tax`,
        (select 
                `ro_cancel_external_ro`.`date_of_cancel`
            from
                (`ro_cancel_external_ro`
                join `ro_am_external_ro` ON ((`ro_am_external_ro`.`id` = `ro_cancel_external_ro`.`ext_ro_id`)))
            where
                ((`ro_cancel_external_ro`.`cancel_ro_by_admin` = 1)
                    and (`ro_am_external_ro`.`cust_ro` = `rnrr`.`customer_ro_number`))) AS `Cancelled`
    from
        `ro_network_ro_report_details` `rnrr`)
		
