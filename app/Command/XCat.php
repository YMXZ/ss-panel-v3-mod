<?php

namespace App\Command;

/***
 * Class XCat
 * @package App\Command
 */

use App\Models\User;
use App\Utils\Hash,App\Utils\Tools,App\Services\Config;

use App\Utils\GA;

class XCat
{

    public $argv;

    public function __construct($argv)
    {
        $this->argv = $argv;
    }

    public function boot(){
        switch($this->argv[1]){
            case("install"):
                return $this->install();
            case("createAdmin"):
                return $this->createAdmin();
            case("resetTraffic"):
                return $this->resetTraffic();
			case("setTelegram"):
                return $this->setTelegram();
            case("sendDiaryMail"):
                return DailyMail::sendDailyMail();
			case("reall"):
                return DailyMail::reall();
			case("syncusers"):
                return SyncRadius::syncusers();
			case("synclogin"):
                return SyncRadius::synclogin();
			case("syncvpn"):
                return SyncRadius::syncvpn();
			case("nousers"):
                return ExtMail::sendNoMail();
			case("oldusers"):
                return ExtMail::sendOldMail();
			case("syncnode"):
                return Job::syncnode();
			case("syncnasnode"):
                return Job::syncnasnode();
			case("syncnas"):
                return SyncRadius::syncnas();
			case("dailyjob"):
				return Job::DailyJob();
			case("checkjob"):
				return Job::CheckJob();
			case("syncduoshuo"):
				return Job::SyncDuoshuo();
			case("userga"):
				return Job::UserGa();
			case("backup"):
				return Job::backup();
            default:
                return $this->defaultAction();
        }
    }

    public function defaultAction(){
        echo "Memo";
    }

    public function install(){
        echo "x cat will install ss-panel v3...../n";
    }

    public function createAdmin(){
        echo "add admin/ 创建管理员帐号.....";
        // ask for input
        fwrite(STDOUT, "Enter your email/输入管理员邮箱: ");
        // get input
        $email = trim(fgets(STDIN));
        // write input back
        fwrite(STDOUT, "Enter password for: $email / 为 $email 添加密码 ");
        $passwd = trim(fgets(STDIN));
        echo "Email: $email, Password: $passwd! ";
        fwrite(STDOUT, "Press [Y] to create admin..... 按下[Y]确认来确认创建管理员账户..... ");
        $y = trim(fgets(STDIN));
        if ( strtolower($y) == "y" ){
            echo "start create admin account";
            // create admin user
            // do reg user
            $user = new User();
            $user->user_name = "admin";
            $user->email = $email;
            $user->pass = Hash::passwordHash($passwd);
            $user->passwd = Tools::genRandomChar(6);
            $user->port = Tools::getLastPort()+1;
            $user->t = 0;
            $user->u = 0;
            $user->d = 0;
            $user->transfer_enable = Tools::toGB(Config::get('defaultTraffic'));
            $user->invite_num = Config::get('inviteNum');
            $user->ref_by = 0;
            $user->is_admin = 1;
			$user->expire_in=date("Y-m-d H:i:s",time()+Config::get('user_expire_in_default')*86400);
			$user->reg_date=date("Y-m-d H:i:s");
			$user->money=0;
			$user->im_type=1;
			$user->im_value="";
			$user->class=0;
			$user->plan='A';
			$user->node_speedlimit=0;
			$user->theme=Config::get('theme');



		$ga = new GA();
                $secret = $ga->createSecret();
                $user->ga_token=$secret;
                $user->ga_enable=0;



            if ($user->save()){
                echo "Successful/添加成功!";
                return true;
            }
            echo "添加失败";
            return false;
        }
        echo "cancel";
        return false;
    }

    public function resetTraffic(){
        try{
            User::where("enable",1)->update([
                'd' => 0,
                'u' => 0,
            ]);
        }catch (\Exception $e){
             echo $e->getMessage();
             return false;
        }
        return "reset traffic successful";
    }
	
	
	public function setTelegram(){
        $bot = new \TelegramBot\Api\BotApi(Config::get('telegram_token'));
		echo $bot->setWebhook(Config::get('baseUrl')."/telegram_callback");
    }
}
