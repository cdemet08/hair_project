/* 
 * Copyright (C) 2013 peredur.net
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

function formhash(form, password) {
    // Create a new element input, this will be our hashed password field. 
    var p = document.createElement("input");

    // Add the new element to our form. 
    form.appendChild(p);
    p.name = "p";
    p.type = "hidden";
    p.value = password.value;

    // Make sure the plaintext password doesn't get sent. 
    password.value = "";

    // Finally submit the form. 
    form.submit();
}



function regform(form, email, password, conf, name, lastname, age, phone ,sex) {
    // Check each field has a value
    if (email.value == '' || password.value == '' || conf.value == '') {
        alert('You must provide all the requested details. Please try again');
        return false;
    }
    
    if (name.value == '' || lastname.value == '' || age.value == '' ) {
        alert('You must provide all the requested details. Please try again');
        return false;
    }


    if (phone.value == '' || sex.value == '' ) {
        alert('You must provide all the requested details. Please try again');
        return false;
    }


   /*
    if (sex.value == 'Male'){
    
        sex.value='0';
    } 
    else {
        sex.value='1';
    }

*/


    
    re = /^\w+$/; 
    if(!re.test(form.password.value)) { 
        alert("password must contain only letters, numbers and underscores. Please try again"); 
        form.password.focus();
        return false; 
    }

    
    
    // Check password and confirmation are the same
    if (password.value != conf.value) {
        alert('Your password and confirmation do not match. Please try again');
        form.password.focus();
        return false;
    }
    
        
    // Create a new element input, this will be our hashed password field. 
    var p = document.createElement("input");

    // Add the new element to our form. 
    form.appendChild(p);
    p.name = "p";
    p.type = "hidden";
    p.value = password.value;

    // Make sure the plaintext password doesn't get sent. 
    password.value = "";
    conf.value = "";

    // Finally submit the form. 
    form.submit();
    return true;
}