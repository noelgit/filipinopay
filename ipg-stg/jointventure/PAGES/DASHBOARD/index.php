<?php  
    switch ($_SESSION['JV']['USER_CODE']) {
        
        case 'JV'://Joint Venture
            include('PAGES/DASHBOARD/joint-venture.php');
            break;
        
        case 'IS'://IPG SUPPORT
            include('PAGES/DASHBOARD/ipg-support.php');
            break;

        default:
            echo "<script>
               window.location.href = '".BASE_URL."';
            </script>"; 
            exit();
            break;
    }
?>