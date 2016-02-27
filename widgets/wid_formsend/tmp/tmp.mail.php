<?php
use maze\helpers\Html;
?>
<h1><?=$thema?></h1>
<table>
    <tbody>
        <tr>
            <td>Имя</td>
            <td><?=$modelForm->name?></td>
        </tr>
        <tr>
            <td>email</td>
            <td><?=$modelForm->email?></td>
        </tr>
        <tr>
            <td>Телефон</td>
            <td><?=$modelForm->phone?></td>
        </tr>
        <tr>
            <td>Текст сообщения</td>
            <td><?=$modelForm->text?></td>
        </tr>
    </tbody>
</table>

