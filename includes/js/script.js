/*

My Custom JS
============

Author:  Brad Hussey
Updated: August 2013
Notes:	 Hand coded for Udemy.com

*/


$(function () {

    $('#alertMe').click(function (e) {
        e.preventDefault();
        $('#successAlert').slideDown();
    });

});