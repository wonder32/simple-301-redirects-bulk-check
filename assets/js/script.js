let csv = {};

function fileInfo(e) {
    var file = e.target.files[0];
    if (file.name.split(".")[1].toUpperCase() != "CSV") {
        alert('Invalid csv file !');
        e.target.parentNode.reset();
        return;
    } else {
        document.getElementById('file_info').innerHTML = "<p>" + simple_check.file_name + " " + file.name + " | " + file.size + " Bytes.</p>";
    }
}

function handleFileSelect() {
    var file = document.getElementById("the_file").files[0];
    var reader = new FileReader();
    var link_reg = /(http:\/\/|https:\/\/)/i;
    reader.onload = function (file) {
        var content = file.target.result;
        var rows = file.target.result.split(/[\r\n|\n]+/);
        var first_column = new Array();
        for (var i = 0; i < rows.length; i++) {
            var arr = rows[i].split(/[;,]/);
            first_column.push(arr[0]);
        }
        var all_urls = file.target.result.split(/[\r\n|\n|,|;]+/);

        let findDuplicates = (arr) => arr.filter((item, index) => arr.indexOf(item) != index)
        let doubles = findDuplicates(all_urls);

        let all_unique = (a) => a.filter(function(item, pos) {
            return a.indexOf(item) == pos;
        })
        let all = all_unique(doubles);

        var table = document.getElementById("simple-bulk-check-table").getElementsByTagName('tbody')[0];
        table.innerHTML = '';
        for (var i = 0; i < rows.length; i++) {

            var arr = rows[i].split(/[;,]/);

            if (typeof arr !== 'undefined' && arr.length > 1) {
                var tr = document.createElement('tr');
                tr.id = 'rule-' + i;

                csv[i] = arr;

                let check = document.createElement('td');
                let checkBox = document.createElement('input');
                checkBox.type = 'checkbox';
                checkBox.checked = true;
                checkBox.className = 'verify-row';
                checkBox.id = 'check-' + i;
                check.appendChild(checkBox);
                tr.appendChild(check);
                let text = '';
                for (var j = 0; j < arr.length; j++) {

                    var td = document.createElement('td');
                    let group_id = all.indexOf(arr[j]);
                    let group_id_first = first_column.indexOf(arr[j]);
                    if (group_id != -1 && group_id_first != -1) {
                        td.classList.add('group-id-' + group_id);
                        text = simple_check.possible_infinite;
                    }

                    if (arr[0] == arr[1]) {
                        text = simple_check.infinite_loop;
                        checkBox.checked = false;
                    }

                    if (link_reg.test(arr[j])) {
                        var a = document.createElement('a');
                        a.href = arr[j];
                        a.target = "_blank";
                        a.innerHTML = arr[j];
                        td.appendChild(a);
                    } else {
                        td.innerHTML = arr[j];
                    }
                    tr.appendChild(td);
                }

                let result = document.createElement('td');
                result.id = 'result-' + i;
                result.innerHTML = text;
                let spinner = document.createElement('img');
                spinner.style.display = 'none';
                spinner.src = simple_check.spinner;
                spinner.classList.add('spinner-load');
                result.appendChild(spinner);
                tr.appendChild(result);

                table.appendChild(tr);
            }
        }
        document.getElementById('simple-bulk-check-table').appendChild(table);
        document.getElementById('verifiy-result').innerHTML = '<div id="check-selectedbutton">' + simple_check.check_selected + '</div>';
    };
    reader.readAsText(file);
}

function selectAll(e) {
    if(e.target && e.target.id == 'check-all') {
        if (e.target.checked) {
            jQuery("[id^=check-]").attr('checked','checked');
        } else {
            jQuery('input:checkbox').removeAttr('checked');
        }
    }
}
function checkSelected (e) {
    if(e.target && e.target.id== 'check-selectedbutton') {

        let check_ids = [];

        jQuery("tbody tr:visible input:checkbox:checked").each(function(){
            check_ids.push(jQuery(this).attr('id').replace('check-', ''));
        });

        let p = jQuery.when();
        check_ids.forEach(function(id) {
            p = p.then(function() {
                 return requestUrl(id);
            });
        });

    }
}

function requestUrl(id) {
    return jQuery.ajax({
            // we get my_plugin.ajax_url from php, ajax_url was the key the url the value
            url: simple_check.ajax_url,
            type: 'post',
            data: {
                // remember_setting should match the last part of the hook (2) in the php file (4)
                action: 'check_url',
                nonce: simple_check.ajax_nonce,
                urls: csv[id],
                id: id
            },
            // if successfull show the result in the console
            // you could append the outcome in the html of the
            // page
            beforeSend: function() {
                jQuery('#result-' + id + ' .spinner-load').show();
            },
            complete: function(){
                jQuery('#result-' + id + ' .spinner-load').hide();
            },
            success: function (response) {
                displayresults(response);
            }
    });
}

function displayresults(response) {

    if (response['status'] == '301' && response['redirect'] == csv[response['id']][1]) {
        jQuery('#result-' + response['id']).text(simple_check.succes);
    } else if (response['status'] == '301') {
        jQuery('#result-' + response['id']).text(simple_check.different);
    } else if (response['status'] == '302' && response['redirect'] == csv[response['id']][1]) {
        jQuery('#result-' + response['id']).text(simple_check.tem_succes);
    } else if (response['status'] == '302') {
        jQuery('#result-' + response['id']).text(simple_check.tem_fail);
    } else if (response['status'] == '200') {
        jQuery('#result-' + response['id']).text(simple_check.fail);
    } else  {
        jQuery('#result-' + response['id']).text('FAIL ' + response['status']);
    }
}

function mouseover(e){
    if(e.target && e.target.matches('td[class^="group-id-"]')) {
        let eclass = jQuery(e.target).attr('class');
        jQuery('.' + eclass).css("background-color", "orange");
    }
}

function mouseout(e){
    if(e.target && e.target.matches('td[class^="group-id-"]')) {
        let eclass = jQuery(e.target).attr('class');
        jQuery('.' + eclass).css("background-color", "");
    }
}

function filterList() {
    // Declare variables
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("filterlist");
    filter = input.value.toUpperCase();
    table = document.getElementById("simple-bulk-check-table");
    tr = table.getElementsByTagName("tr");
    console.table(tr);

    // Loop through all table rows, and hide those who don't match the search query
    for (i = 0; i < tr.length; i++) {
        td_a = tr[i].getElementsByTagName("td")[1];
        td_b = tr[i].getElementsByTagName("td")[2];
        if (td_a && td_b) {
            txtValueA = td_a.textContent || td_a.innerText;
            txtValueB = td_b.textContent || td_b.innerText;
            if (txtValueA.toUpperCase().indexOf(filter) > -1 || txtValueB.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";

            }
        }
    }
}

jQuery(document).ready(function () {
    document.getElementById('the_form').addEventListener('submit', handleFileSelect, false);
    document.getElementById('the_file').addEventListener('change', fileInfo, false);
    document.addEventListener('click',function(e){checkSelected(e)});
    document.addEventListener('click',function(e) {selectAll(e); });
    document.addEventListener('mouseover',function(e) {mouseover(e); });
    document.addEventListener('mouseout',function(e) {mouseout(e); });
});

