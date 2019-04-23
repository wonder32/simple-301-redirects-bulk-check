var csv = {};

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

                for (var j = 0; j < arr.length; j++) {

                    var td = document.createElement('td');

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
                tr.appendChild(result);

                table.appendChild(tr);
            }
        }
        document.getElementById('simple-bulk-check-table').appendChild(table);
        document.getElementById('verifiy-result').innerHTML = '<div id="check-selectedbutton">Check selected</div>';
    };
    reader.readAsText(file);
}

function checkSelected (e) {
    if(e.target && e.target.id== 'check-selectedbutton') {

        jQuery("tbody input:checkbox:checked").each(function(){
            let check_id = jQuery(this).attr('id').replace('check-', '');
            let result = requestUrl(check_id);
        });

    }
}

function requestUrl(id) {

    let old_url = csv[id][0];
    let new_url = csv[id][1];
    jQuery.ajax({
        url:      old_url,
        dataType: 'text',
        type:     'GET',
        complete:  function(xhr){
            console.table(xhr);
        }
    });
}

jQuery(document).ready(function () {
    document.getElementById('the_form').addEventListener('submit', handleFileSelect, false);
    document.getElementById('the_file').addEventListener('change', fileInfo, false);

    document.addEventListener('click',function(e){'click', checkSelected(e)});

});