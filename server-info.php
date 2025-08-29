<?php
use WHMCS\View\Menu\Item as MenuItem;
use Illuminate\Database\Capsule\Manager as Capsule;

/* Add credentials to the end of all secondary sidebars. */
/* This Hook Has been Modified by YASH HOST */
/* Under GPL License */
add_hook('ClientAreaSecondarySidebar', 1, function (MenuItem $secondarySidebar) {
    /* Get the credentials. */
    $service = Menu::context('service');
    $username = "{$service->username}";
    $serverid = "{$service->server}";
    $domain = "{$service->domain}";
    $password = "{$service->password}";
    $server = Capsule::table('tblservers')->where('id', '=', $serverid)->pluck('hostname');
    $ipaddress = Capsule::table('tblservers')->where('id', '=', $serverid)->pluck('ipaddress');
    $name1 = Capsule::table('tblservers')->where('id', '=', $serverid)->pluck('nameserver1');
    $name2 = Capsule::table('tblservers')->where('id', '=', $serverid)->pluck('nameserver2');

    $password = decrypt($password);
    /* If the username isn't empty let's show them! */
    if ($username != '') {
        $secondarySidebar->addChild('credentials', array(
            'label' => 'Service Information',
            'uri' => '#',
            'icon' => 'fa-desktop',
        ));
        $credentialPanel = $secondarySidebar->getChild('credentials');
        $credentialPanel->moveToBack();
        $credentialPanel->addChild('username', array(
            'label' => $username,
            'order' => 1,
            'icon' => 'fa-user',
        ));
        $credentialPanel->addChild('password', array(
            'label' => $password,
            'order' => 2,
            'icon' => 'fa-lock',
        ));
        $credentialPanel->addChild('domain', array(
            'label' => $domain,
            'order' => 3,
            'icon' => 'fa-globe',
        ));

        $serverRow = Capsule::table('tblservers')->where('id', '=', $serverid)->first(); // Retrieve the server data row

        if ($serverRow) {
            $serverHostname = $serverRow->hostname;

            $secondarySidebar->addChild('serverInfo', array(
                'label' => 'Server Information',
                'uri' => '#',
                'icon' => 'fa-server',
            ));

            $serverInfoPanel = $secondarySidebar->getChild('serverInfo');

            $serverInfoPanel->addChild('ip', array(
                'label' => $ipaddress,
                'order' => 1,
                'icon' => 'fa-info',
            ));
            
            $serverInfoPanel->addChild('name1', array(
                'label' => $name1,
                'order' => 3,
                'icon' => 'fa-info-circle',
                'onclick' => "copyTextToClipboard('{$name1}');",
            ));
            $serverInfoPanel->addChild('name2', array(
                'label' => $name2,
                'order' => 4,
                'icon' => 'fa-info-circle',
                'onclick' => "copyTextToClipboard('{$name2}');",
            ));
        } else {
            $secondarySidebar->addChild('serverInfo', array(
                'label' => 'Server Information',
                'uri' => '#',
                'icon' => 'fa-server',
            ));
            $serverInfoPanel = $secondarySidebar->getChild('serverInfo');
            $serverInfoPanel->addChild('error', array(
                'label' => 'Error: Server details not found',
                'order' => 1,
                'icon' => 'fa-exclamation-circle',
            ));
        }
    }
});
?>

<script>
function copyTextToClipboard(text) {
    var textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed'; // Ensure the textarea is visible
    document.body.appendChild(textarea);
    textarea.focus();
    textarea.select();

    try {
        var successful = document.execCommand('copy');
        var msg = successful ? 'successful' : 'unsuccessful';
        console.log('Copying text command was ' + msg);
    } catch (err) {
        console.error('Unable to copy', err);
    }

    document.body.removeChild(textarea);
}
</script>
