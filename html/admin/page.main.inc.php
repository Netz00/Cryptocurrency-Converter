<?php


if (!admin::isSession()) {

    header("Location: /admin/login",  true,  301);
    exit;
}

$error = false;
$error_message = '';

$cryptocoin = new cryptocoin($dbo);

if (!empty($_POST)) {

    $state = isset($_POST['state']) ? $_POST['state'] : 0;
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $symbol = isset($_POST['symbol']) ? $_POST['symbol'] : '';
    $codename = isset($_POST['codename']) ? $_POST['codename'] : '';
    $token = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';



    $state = helper::clearText($state);
    $name = helper::clearText($name);
    $symbol = helper::clearText($symbol);
    $codename = helper::clearInt($codename);


    $state = helper::escapeText($state);
    $name = helper::escapeText($name);
    $symbol = helper::escapeText($symbol);


    if (helper::getAuthenticityToken() !== $token) {

        $error = true;
        $error_message = 'Error!';
    }

    if (!$error) {

        $access_data = array();

        $access_data = $cryptocoin->create($name, $symbol, $codename, $state);

        if ($access_data['error'] === true) {

            $error = true;
            $error_message = 'Incorrect cryptocoin data.';
        }
    }
}

$cryptocoins = $cryptocoin->get();

helper::newAuthenticityToken();

$page_title = "Admin| Main";


include_once("../html/common/admin_header.inc.php");

?>

<body>
    <div class="allButFooter">
        <?php

        include_once("../html/common/admin_topbar.inc.php");

        ?>

        <div class="table_container">
            <form>
                <input type="text" id="name" placeholder="Name">
                <input type="text" id="symbol" placeholder="Symbol">
                <input type="text" id="codename" placeholder="Codename">
                <input type="button" class="add-row" value="Add Row">
            </form>
            <table>
                <thead>
                    <tr>
                        <th>Select</th>
                        <th>Name</th>
                        <th>Symbol</th>
                        <th>Codename</th>
                        <th>State</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <!-- Example of row
                        <td><input type="checkbox" name="record" id="0"></td>
                        <td name="name">Bitcoin</td>
                        <td>BTC</td>
                        <td>BTCUSDT</td>
                        <td>
                            <label class="switch" id="0">
                                <input type="checkbox" checked>
                                <span class="slider round"></span>
                            </label>
                        </td>
                        -->
                    </tr>
                </tbody>
            </table>
            <button type="button" class="delete-row">Delete Row</button>

        </div>

    </div>


    <script>
        var cryptocoins_raw = '<?php echo json_encode($cryptocoins); ?>';
        var cryptocoins = JSON.parse(cryptocoins_raw);

        $(document).ready(function() {

            // Load stored cryptocoins

            if (cryptocoins.error)
                console.error("Couldn't fetch invoices from database.");
            else
                cryptocoins.items.forEach(data => {
                    appendRowToTable(data);
                });

            // Add row

            $(".add-row").click(function() {
                var name = $("#name").val();
                var symbol = $("#symbol").val();
                var codename = $("#codename").val();

                addRowSave({
                    name,
                    symbol,
                    codename
                });
                appendRowToTable({
                    name,
                    symbol,
                    codename
                });

            });


            // Find and remove selected table rows
            $(".delete-row").click(function() {

                let rowsToBeDeleted = [];

                $("table tbody").find('input[name="record"]').each(function() {
                    if ($(this).is(":checked")) {
                        rowsToBeDeleted.push($(this).attr('id'));
                        $(this).parents("tr").remove();
                    }
                });

                var rowsIDs = JSON.stringify(rowsToBeDeleted);
                deleteRowSave({
                    rowsIDs
                });
            });

            let counter = 0;

            // Change row state

            $(".switch").click(function(e) {
                counter++;

                if (counter % 2 == 0)
                    return;

                const id = e.currentTarget.id;
                const state = e.currentTarget.querySelector('input').checked ? 0 : 1;

                setStateRowSave({
                    id,
                    state
                });

            });

        });


        const setStateRowSave = (data) => {
            $.ajax({
                type: "POST",
                url: "/api/v1/method/account.setStateCryptocurrency",
                data,
                dataType: "json",
                success: function(data) {
                    console.log(data);
                    if (data.error)
                        console.log(data.error);
                }
            });
        };

        const deleteRowSave = (data) => {
            $.ajax({
                type: "POST",
                url: "/api/v1/method/account.deleteCryptocurrency",
                data,
                dataType: "json",
                success: function(data) {
                    console.log(data);
                    if (data.error)
                        console.log(data.error);
                }
            });
        };


        const addRowSave = (data) => {
            $.ajax({
                type: "POST",
                url: "/api/v1/method/account.addCryptocurrency",
                data,
                dataType: "json",
                success: function(data) {
                    console.log(data);
                    if (data.error)
                        console.log(data.error);
                }
            });
        };


        const appendRowToTable = (data) => {
            var markup = `<tr>
                                    <td><input type='checkbox' name='record' id='${data.id}'></td>
                                    <td>${data.name}</td>
                                    <td>${data.symbol}</td>
                                    <td>${data.codename}</td>
                                    <td>
                                        <label class="switch" id='${data.id}'>
                                            <input type="checkbox" ${data.state==="1" && 'checked'}>
                                            <span class="slider round"></span>
                                        </label>
                                    </td>  
                                </tr>`;
            $("table tbody").append(markup);
        };
    </script>
</body>

</html>