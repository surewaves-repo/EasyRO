var Utility = {
    //Check word length
    checkWordLength: function (a) {
        var words = a.split(' ' || '\n');
        for (var i = 0; i < words.length; i++) {
            if (words[i].length > 45) {
                return false;
            }
        }
        return true;
    },
    checkTextWordLength: function (a) {
        if (a.length > 1000) {
            return false;
        } else {
            return true;
        }
        /*var words = a.split(' ' || '\n');
        for (var i=0; i<words.length; i++ ) {
            if ( words[i].length > 1000) {
                    return false;
            }
        } */

    },
    checkTextWordLengthForAgency: function (a) {
        if (a.length > 75) {
            return false;
        } else {
            return true;
        }

    },
    checkNumbersAndDOT: function (a) {
        //97 122 65 90 48 57 46 95
        for (var i = 0; i < a.length; i++) {
            if ((a.charCodeAt(i) >= 48 && a.charCodeAt(i) <= 57) || a.charCodeAt(i) === 46) {
                continue;
            } else {
                return false;
            }
        }
        return true;

    },
    checkMaxChar: function (no_of_char, id) {
        if ($F(id).length >= no_of_char) {
            return false;
        }
        return true;
    },
    validateEmail: function (str) {
        var objRegExp = /^[A-Za-z0-9]+[\w.-_]*?[A-Za-z0-9]+@[A-Za-z0-9]+[\w.-]*?(.[A-Za-z0-9]{2,5}){1}(.[a-z]{2,3}){1}$/;
        return objRegExp.test(str);
    },
    checkSpace: function (a) {
        for (var i = 0; i < a.length; i++) {
            if (a.charCodeAt(i) === 32) {
                return false;
            }
        }
        return true;
    },
    checkUnderScore: function (a) {
        var prev = 0;
        for (var i = 0; i < a.length; i++) {
            if (a.charCodeAt(i) === 95) {
                if (prev === 95) {
                    return false;
                }
            }
            prev = a.charCodeAt(i);
        }
        return true;
    },
    checkSpecialCharacter: function (a) {
        for (var i = 0; i < a.length; i++) {
            if ((a.charCodeAt(i) >= 97 && a.charCodeAt(i) <= 122) ||
                (a.charCodeAt(i) >= 65 && a.charCodeAt(i) <= 90) ||
                (a.charCodeAt(i) >= 48 && a.charCodeAt(i) <= 57) ||
                (a.charCodeAt(i) === 32 || a.charCodeAt(i) === 10 || a.charCodeAt(i) === 09
        ))
            {
                continue;
            }
        else
            {
                return false;
            }
        }
        return true;

    },
    checkInteger: function (a) {
        //97 122 65 90 48 57 46 95
        for (var i = 0; i < a.length; i++) {
            if ((a.charCodeAt(i) >= 48 && a.charCodeAt(i) <= 57)) {
                continue;
            } else {
                return false;
            }
        }
        return true;
    },
    checkAlphabet: function (a) {
        var reg1 = /.*[a-zA-Z].*/;
        if ((reg1.test(a)) && (a.length > 0)) {
            return true;
        } else {
            return false;
        }
        return true;
    },
    checkAlphaNumeric: function (a) {
        var reg1 = /.*[a-zA-Z0-9].*/;
        if ((reg1.test(a)) && (a.length > 0)) {
            return true;
        } else {
            return false;
        }
        return true;
    },

    checkInput1: function (a) {
        //check input for alphanumeric and -  _  . sp
        var objRegExp = /^[a-zA-Z0-9-_.(,:) ]*$/;
        if ((objRegExp.test(a)) && (a.length > 0)) {
            return true;
        } else {
            return false;
        }
        return true;
    },
    checkInputAddress: function (a) {
        //check input for alphanumeric and -  _  . sp
        var objRegExp = /^[a-zA-Z0-9-_.(,: /) ]*$/;
        if ((objRegExp.test(a)) && (a.length > 0)) {
            return true;
        } else {
            return false;
        }
        return true;
    },

    checkInput2: function (a) {
        //check input for alphanumeric and -  _  . sp
        var objRegExp = /^[a-zA-Z0-9-_. ]*$/;
        if ((objRegExp.test(a)) && (a.length > 0)) {
            return true;
        } else {
            return false;
        }
        return true;
    },

    checkInput3: function (a) {
        //check input for alphanumeric and -  _  . :
        var objRegExp = /^[a-zA-Z0-9-_.:]*$/;
        if ((objRegExp.test(a)) && (a.length > 0)) {
            return true;
        } else {
            return false;
        }
        return true;
    },

    checkInput4: function (a) {
        //check input for alphanumeric and -  _  /  . sp
        var objRegExp = /^[a-zA-Z0-9-_\/. ]*$/;
        if ((objRegExp.test(a)) && (a.length > 0)) {
            return true;
        } else {
            return false;
        }
        return true;
    },

    checkInput5: function (a) {
        //check input for alphanumeric and -  _  . no space
        var objRegExp = /^[a-zA-Z0-9\-_\.]*$/;
        if ((objRegExp.test(a)) && (a.length > 0)) {
            return true;
        } else {
            return false;
        }
        return true;
    },

    checkSingleDoubleQuotes: function (a) {
        //check input for Single and Double Quotes
        var objRegExp = /^[^'"]*$/;
        if ((objRegExp.test(a)) && (a.length > 0)) {
            return true;
        } else {
            return false;
        }
        return true;
    },
    checkSingleDoubleQuotes_v1: function (a) {
        //check input for Single and Double Quotes
        var objRegExp = /^[^'"]*$/;
        if ((objRegExp.test(a))) {
            return true;
        } else {
            return false;
        }
        return true;
    },
    checkAmount: function (a) {
        //check input for amount
        var objRegExp = /^[0-9.]*$/;
        if ((objRegExp.test(a)) && (a.length > 0)) {
            return true;
        } else {
            return false;
        }
        return true;
    },

    checkContactNoLength: function (a) {
        //check contact no to be atleast 10 characters
        if (a.length >= 10) {
            return true;
        } else {
            return false;
        }
        return true;
    },
    checkAddress: function (a) {
        //check input for alphanumeric and -  _  . / sp
        var objRegExp = /^[a-zA-Z0-9-_\/., ]*$/;
        if ((objRegExp.test(a)) && (a.length > 0)) {
            return true;
        } else {
            return false;
        }
        return true;
    },
    checkSpecialCharacterWithComma: function (a) {
        /*S(97 to 122 for a to z)(65 to 90 for A to Z)48 57 46 95
        (44 for ,) (45 for-)(_95)(space=32)*/
        for (var i = 0; i < a.length; i++) {
            if ((a.charCodeAt(i) >= 97 && a.charCodeAt(i) <= 122) ||
                (a.charCodeAt(i) >= 65 && a.charCodeAt(i) <= 90) ||
                (a.charCodeAt(i) === 44) || (a.charCodeAt(i) === 45) ||
                (a.charCodeAt(i) === 32) || (a.charCodeAt(i) === 46) ||
                (a.charCodeAt(i) >= 48 && a.charCodeAt(i) <= 57) ||
                (a.charCodeAt(i) === 46 || a.charCodeAt(i) === 95)) {
                continue;
            } else {
                return false;
            }
        }
        return true;
    },
    checkInvoice: function (a) {
        //check input for alphanumeric and -  _  . :
        var objRegExp = /^[a-zA-Z0-9-_.:\/ ]*$/;
        if ((objRegExp.test(a)) && (a.length > 0)) {
            return true;
        } else {
            return false;
        }
        return true;
    }
};