<?php
if (session_id() === "") {
    session_start();
}
?>
<header>
    <div class="container">
        <div class="navbar-header">
            <button class="navbar-toggle">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?php echo $config->rootDir; ?>"><img src="<?php echo $config->rootDir; ?>/static/img/aci-logo.svg" alt="Home"></a>
        </div>

        <div class="navbar-collapse">
            <ul class="navbar-nav">
                <li class="dropdown">
                    <span class="dropdown-toggle">connector <span class="caret"></span>
                    </span>
                    <ul class="dropdown-menu">
                        <?php
                            //all orgs + user
                            if (isset($_SESSION["orgs"]) && !empty($_SESSION["orgs"])) {
                            }
                            else {
                                echo '<li><a href="' . $config->rootDir .'/Add">+ Add Connector</a></li>';
                                foreach (glob(__DIR__ . '/../connectors/*.php') as $file) {
                                    $conStr = str_replace(__DIR__  . "/../connectors/", "", $file);
                                    $conStr = str_replace("Connector.php", "", $conStr);
                                    echo '<li><a href="' . $config->rootDir . '/' . $conStr . '">' . $conStr . '</a></li>';
                                }
                                
                            }
                        ?>
                    </ul>
                </li>
                <!-- <li>
                    <input class="doxyfySearch" type="search" value="" placeholder="search" />
                </li>   -->   
                <li class="dropdown">
                    <span class="dropdown-toggle">docs <span class="caret"></span>
                    </span>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo $config->rootDir;?>/docs/workflows">Workflows</a></li>
                    </ul>
                </li>              
            </ul>

            <!-- <ul class="navbar-right">
                <li role="presentation" class="dropdown">
                    <a class="dropdown-toggle userImage" data-toggle="dropdown" href="#">
                    <img src="<?php echo $config->rootDir; ?>/static/img/account/user.png" width="40px" />
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <div class="profileDropdown">
                                <div class="profileLeft">
                                    <img src="<?php echo $config->rootDir; ?>/static/img/account/user.png" width="100px" />
                                </div>
                                <div class="profileRight">
                                    <span><?php //echo $_SESSION["userName"]; ?></span>
                                    <ul>
                                        <li><a href="<?php echo $config->rootDir; ?>/organisations">My Organisations</a></li>
                                        <li><a href="#">My Profile</a></li>
                                        <li><a href="<?php echo $config->rootDir; ?>/logout">Logout</a></li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul> -->
        </div>
    </div>
</header>