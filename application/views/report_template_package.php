<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>REPORT MAIL</title>
    <style type="text/css">
        body {
            font-family: "Open Sans", sans-serif;
            line-height: 1.25;
        }

        table {
            border: solid 1px #DDEEEE;
            border-collapse: collapse;
            border-spacing: 0;
            font: normal 13px Arial, sans-serif;
            width: 100%;
        }
        table thead th {
            background-color: #DDEFEF;
            border: solid 1px #DDEEEE;
            color: #336B6B;
            padding: 10px;
            text-align: left;
            text-shadow: 1px 1px 1px #fff;
        }
        table tbody td {
            border: solid 1px #DDEEEE;
            color: #333;
            padding: 10px;
            text-shadow: 1px 1px 1px #fff;
        }

        table caption {
            font-size: 1.5em;
            margin: .5em 0 .75em;
        }

        footer{
            text-align: center;
            font-size: 1em;
        }
        footer > p {
            padding: 0px;
            margin: 0px;
        }
        footer > p > b{
            color: #8eafe2;
        }
        footer > p > i{
            color: #F2C112;
        }

        .caption-table{
            font-size: 0.9em;
            margin: .5em 0 .75em;
        }

        .container-header{
            text-align: center;
            border: 5px dashed #757575;
            margin-bottom: 20px;
            margin-left: 10px;
            margin-right: 10px;
            margin-top: 10px;
            padding: 20px;
        }
        .header{
            color: #8eafe2;
            font-size: 32px;
            font-weight: bold;
            padding: 0px;
            margin:0px;
        }
        .subheader{
            padding: 0px;
            margin:0px;
            color: #616161;
        }

        .btn-download {
            text-decoration: none;
            color: #333;
            background-color: #8eafe2;
            padding: 20px; 
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container-header">
        <p class="header">PD MASTERINDO LAJU ABADI</p>
        <p class="subheader">Jl. Maulana hasanudin no.98 Cipondoh, Tangerang, kota Tangerang Banten, 15148.</p>
    </div>
    <div align="center">
        <div class="caption-table">
            (<?=count($package);?>) <?=$title;?><br>
            <small><i>periode <?=$period;?></i></small>
        </div>
        <table>
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">CUSTOMER</th>
                    <th scope="col">ISI PAKET</th>
                    <th scope="col">TUJUAN<br>(ALAMAT)</th>
                    <th scope="col">LAST UPDATE</th>
                    <th scope="col">STATUS</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($package as $k => $v) { ?>
                    <tr>
                        <td><?=($k+1);?></td>
                        <td><?=$v['customer_nama'];?></td>
                        <td><?=$v['package_nama'];?></td>
                        <td><?=$v['package_tujuan'];?><br>(<?=zempty($v['package_alamat']);?>)</td>
                        <td><?=rfdate($v['package_last_update']);?></td>
                        <td><?=$v['package_status'];?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <br><br><br>
        <p align="right" style="margin-right: 50px">
            <?=getCityName();?>, <?=getDayName();?> <?=dateidn();?>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            _________________________________<br>
            PIMPINAN<br>
        </p>
        
        <p>
            <a href="<?=$url_download;?>" class="btn-download">DOWNLOAD REPORT</a>
        </p>
        <br>
        <br>
    </div>
</body>

</html>