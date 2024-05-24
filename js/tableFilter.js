/* Getting check box value */
function getCheckBoxValue(name) {

    var checkedValue = $('input[name="' + name + '"]:checked').val();

    return checkedValue;

}

/* Build column finder */
function getColumnFinder(currentRow, colNumber, checkValue) {

    var value = currentRow.find('td:eq(' + colNumber + ')').text();
    var condition = "";

    if (value != "" && value != null) {

        var newValue = new String(value);
        var newCheckValue = new String(checkValue);

        condition = "( " + newValue + " === " + newCheckValue + " )";

    }

    return condition;

}

function freeTdClass() {

    $("#dataTable tr").each(function () {

        var tr = $(this);
        var parentClass = $(this).attr("class");

        if (parentClass == "parent") {

            var parentId = $(this).attr("id");

            if ($("#B" + parentId).text().trim() == "-") {

                $("#B" + parentId).text('+');
                $('.C' + parentId).hide();
            }

        } else {

            tr.removeClass("filterHidden");

        }

    });


}

function getCondition(oneCon, twoCon, threeCon) {

    var temp;

    if ((oneCon != "" && oneCon != null) || (twoCon != "" && twoCon != null) || (threeCon != "" && threeCon != null)) {

        temp = "(" + oneCon + " && " + twoCon + " && " + threeCon + ")";

    } else {

        temp = "";
    }

    return temp;

}

function getFilterCondition(currentRow) {

    var filterCondition = [];
    var requestValue;
    var oneCon;
    var twoCon;
    var threeCon;
    var temp;

    if ($('input[name="ro"]').is(":checked")) {

        /* Columns according to row 1, 6, 11 */

        requestValue = getCheckBoxValue("ro");

        oneCon = getColumnFinder(currentRow, 1, requestValue);
        twoCon = getColumnFinder(currentRow, 6, requestValue);
        threeCon = getColumnFinder(currentRow, 11, requestValue);

        filterCondition.push(getCondition(oneCon, twoCon, threeCon));

    }

    if ($('input[name="dep"]').is(":checked")) {

        /* Columns according to row 2, 7, 12 */

        requestValue = getCheckBoxValue("dep");
        oneCon = getColumnFinder(currentRow, 2, requestValue);
        twoCon = getColumnFinder(currentRow, 7, requestValue);
        threeCon = getColumnFinder(currentRow, 12, requestValue);

        filterCondition.push(getCondition(oneCon, twoCon, threeCon));

    }
    if ($('input[name="ro_dep"]').is(":checked")) {


        /* Columns according to row 3, 8, 13 */

        requestValue = getCheckBoxValue("ro_dep");
        oneCon = getColumnFinder(currentRow, 3, requestValue);
        twoCon = getColumnFinder(currentRow, 8, requestValue);
        threeCon = getColumnFinder(currentRow, 13, requestValue);

        filterCondition.push(getCondition(oneCon, twoCon, threeCon));
    }

    if ($('input[name="ping"]').is(":checked")) {

        /* Columns according to row 4, 9, 14 */

        requestValue = getCheckBoxValue("ping");
        oneCon = getColumnFinder(currentRow, 4, requestValue);
        twoCon = getColumnFinder(currentRow, 9, requestValue);
        threeCon = getColumnFinder(currentRow, 14, requestValue);

        filterCondition.push(getCondition(oneCon, twoCon, threeCon));

    }
    if ($('input[name="report"]').is(":checked")) {

        /* Columns according to row 5, 10, 15 */

        requestValue = getCheckBoxValue("report");
        oneCon = getColumnFinder(currentRow, 5, requestValue);
        twoCon = getColumnFinder(currentRow, 10, requestValue);
        threeCon = getColumnFinder(currentRow, 15, requestValue);

        filterCondition.push(getCondition(oneCon, twoCon, threeCon));
    }

    return filterCondition;
}

function countMarketChilds() {

    $("#dataTable tr").each(function () {

        var tr = $(this);
        var isParent = tr.hasClass('parent');

        if (isParent === true) {

            var parentId = $(this).attr("id");
            console.log("Parentid--->" + parentId);
            if (parentId != "" && parentId != null) {
                var countColumnsArray = countChild(parentId);
                updateMarketTotal(countColumnsArray, parentId);
            }
        }
    });

}

function countChild(parentId) {

    var childClass = ".C" + parentId;
    var totalArray = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

    $(childClass + ":not(.filterHidden)tr").each(function () {

        totalArray[1] += parseInt($(this).find('td:eq(1)').text());
        totalArray[2] += parseInt($(this).find('td:eq(2)').text());
        totalArray[3] += parseInt($(this).find('td:eq(3)').text());

        totalArray[4] += parseInt($(this).find('td:eq(4)').text());
        totalArray[5] += parseInt($(this).find('td:eq(5)').text());
        totalArray[6] += parseInt($(this).find('td:eq(6)').text());

        totalArray[7] += parseInt($(this).find('td:eq(7)').text());
        totalArray[8] += parseInt($(this).find('td:eq(8)').text());
        totalArray[9] += parseInt($(this).find('td:eq(9)').text());

        totalArray[10] += parseInt($(this).find('td:eq(10)').text());
        totalArray[11] += parseInt($(this).find('td:eq(11)').text());
        totalArray[12] += parseInt($(this).find('td:eq(12)').text());

        totalArray[13] += parseInt($(this).find('td:eq(13)').text());
        totalArray[14] += parseInt($(this).find('td:eq(14)').text());
        totalArray[15] += parseInt($(this).find('td:eq(15)').text());

    });

    return totalArray;

}

function countMarketHeader() {

    var totalArray = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

    $("#dataTable tr").each(function () {

        var parentId = $(this).attr("id");
        var parentClass = $(this).attr("class");

        if (parentClass == "parent") {

            if ((parentId != "" && parentId != null) && parentId != "1_2") {

                totalArray[1] += parseInt($(this).find('td:eq(1)').text());
                totalArray[2] += parseInt($(this).find('td:eq(2)').text());
                totalArray[3] += parseInt($(this).find('td:eq(3)').text());

                totalArray[4] += parseInt($(this).find('td:eq(4)').text());
                totalArray[5] += parseInt($(this).find('td:eq(5)').text());
                totalArray[6] += parseInt($(this).find('td:eq(6)').text());

                totalArray[7] += parseInt($(this).find('td:eq(7)').text());
                totalArray[8] += parseInt($(this).find('td:eq(8)').text());
                totalArray[9] += parseInt($(this).find('td:eq(9)').text());

                totalArray[10] += parseInt($(this).find('td:eq(10)').text());
                totalArray[11] += parseInt($(this).find('td:eq(11)').text());
                totalArray[12] += parseInt($(this).find('td:eq(12)').text());

                totalArray[13] += parseInt($(this).find('td:eq(13)').text());
                totalArray[14] += parseInt($(this).find('td:eq(14)').text());
                totalArray[15] += parseInt($(this).find('td:eq(15)').text());

            }
        }
    });
    console.log("totalArray--->" + totalArray);
    updateMarketTotal(totalArray, "1_2");

}

function updateMarketTotal(totalChildArray, rowId) {

    for (var i = 1; i <= 15; i++) {

        $("#" + rowId).find('td:eq(' + i + ')').text(totalChildArray[i]);

    }

}

function setCheckedBoxState() {

    var checkBoxState = [];
    var checkBoxValue;

    if ($('input[name="ro"]').is(":checked")) {

        checkBoxValue = getCheckBoxValue("ro");
        checkBoxState.push("ro~" + checkBoxValue);
    }
    if ($('input[name="dep"]').is(":checked")) {

        checkBoxValue = getCheckBoxValue("dep");
        checkBoxState.push("dep~" + checkBoxValue);
    }
    if ($('input[name="ro_dep"]').is(":checked")) {

        checkBoxValue = getCheckBoxValue("ro_dep");
        checkBoxState.push("ro_dep~" + checkBoxValue);
    }
    if ($('input[name="ping"]').is(":checked")) {

        checkBoxValue = getCheckBoxValue("ping");
        checkBoxState.push("ping~" + checkBoxValue);
    }
    if ($('input[name="report"]').is(":checked")) {

        checkBoxValue = getCheckBoxValue("report");
        checkBoxState.push("report~" + checkBoxValue);
    }

    $("#filterState").val(checkBoxState);
}

function getCheckedBoxState() {

    return $("#filterState").val();
}

function setLastCheckBoxState(lastCheckBoxState) { // String format name~value with ,

    if (lastCheckBoxState != "" && lastCheckBoxState != null) {

        var stateArray = lastCheckBoxState.split(",");

        for (var i = 0; i < stateArray.length; i++) {

            var nameValue = stateArray[i].split("~");

            checkCheckBox(nameValue[0], nameValue[1]);
        }
    }
}

function checkCheckBox(name, value) {

    $('input[name="' + name + '"][value="' + value + '"]')[0].checked = true;

}