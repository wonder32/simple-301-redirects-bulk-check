let csv = {};

function fileInfo(e) {
    var file = e.target.files[0];
    if (file.name.split(".")[1].toUpperCase() != "CSV") {
        alert('Invalid csv file !');
        e.target.parentNode.reset();
        return;
    } else {
        document.getElementById('file_info').innerHTML = "<p>File Name: " + file.name + " | " + file.size + " Bytes.</p>";
    }
}

function handleFileSelect() {
    var file = document.getElementById("the_file").files[0];
    var reader = new FileReader();
    var link_reg = /(http:\/\/|https:\/\/)/i;
    reader.onload = function (file) {
        var content = file.target.result;
        var rows = file.target.result.split(/[\r\n|\n]+/);
        var all_urls = file.target.result.split(/[\r\n|\n|,]+/);

        let findDuplicates = (arr) => arr.filter((item, index) => arr.indexOf(item) != index)
        let doubles = findDuplicates(all_urls);
        let all_unique = (a) => a.filter(function(item, pos) {
            return a.indexOf(item) == pos;
        })
        let all = all_unique(doubles);
        console.table(all);
        var table = document.getElementById("simple-bulk-check-table").getElementsByTagName('tbody')[0];
        table.innerHTML = '';

        for (var i = 0; i < rows.length; i++) {

            var arr = rows[i].split(',');

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


                    if (arr[0] == arr[1]) {
                        text = 'ERROR SAME LINK';
                    }

                    var td = document.createElement('td');
                    let group_id = all.indexOf(arr[j]);
                    if (group_id != -1) {
                        td.classList.add('group-id-' + group_id);
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
                tr.appendChild(result);

                table.appendChild(tr);
            }
        }
        document.getElementById('simple-bulk-check-table').appendChild(table);
        document.getElementById('verifiy-result').innerHTML = '<div id="check-selectedbutton">Check selected</div>';
    };
    reader.readAsText(file);
}

function selectAll(e) {
    if(e.target && e.target.id == 'check-all') {
        if (e.target.checked) {
            jQuery("[id^=check-]").attr('checked','checked');check-0
        } else {
            jQuery('input:checkbox').removeAttr('checked');
        }
    }
}
function checkSelected (e) {
    if(e.target && e.target.id== 'check-selectedbutton') {

        let check_ids = [];

        jQuery("tbody input:checkbox:checked").each(function(){
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
            success: function (response) {
                console.table(response);
            }
    });
}

jQuery(document).ready(function () {
    document.getElementById('the_form').addEventListener('submit', handleFileSelect, false);
    document.getElementById('the_file').addEventListener('change', fileInfo, false);
    document.addEventListener('click',function(e){checkSelected(e)});
    document.addEventListener('click',function(e) {selectAll(e); });

});