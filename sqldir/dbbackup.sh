d=`date +"%m_%d_%y_%H_%M_%S"`
fl="surgical_mysqldump".$d.$$."dmp";
mysqldump -u surgical_user surgical --password=kb123 > $fl;
