外网超级用户   admin  / admin123

 
id
int(10)

                                                数据库配置              高防
wan_ip                  varchar(16)             外网IP                 
cpu                     tinyint(2)              CUP核数                
memory                  tinyint(2)              内存大小
disk                    smallint(4)             硬盘大小
cabinet_size            varchar(17)             机柜大小
cabinet_position        varchar(17)             机柜位置
assets_code             varchar(40)             资料编号
type                    tinyint(1)              类型  0租用/1托管
band                    smallint(4)             带宽


kz_goodsorder



id int(10) 
number varchar(40)
gid int(10) 

quantity smallint(5)
info varchar(258)
status tinyint(1)
uid int(10)
price decimal(12,2)
createtime int(10) 
paytime int(10)
time int(32)
type tinyint(1)
remark varchar(128)
orderType tinyint(1)
roomid int(11)
cabinetID varchar(40)
IP varchar(200)
startday int(10) 
endday int(10)
svstatus tinyint(1) 
orderstatus tinyint 订单状态  等待确认/(客户经理确认)临时订单/客服确认/试用订单（下派临时工单）/订单核查完毕（工单核发）/正在施工/施工完毕/正式订单/临时终止（归档）/正式终止（归档）//财务到帐/客服终止/  
