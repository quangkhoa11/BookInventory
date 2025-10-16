<?php
    class database{

        private function ketnoi(){
            $conn=new mysqli("localhost","root","","thakho");
            if($conn->connect_error){
                echo "Kết nối thất bại!";
                exit();
            }
            else{
                return $conn;
            }
        }
        //Lê Quang Khoa
        public function xuatdulieu($sql){
            $link=$this->ketnoi();
            $arr=array();
            $result=$link->query($sql);
            if($result->num_rows){
                while($row=$result->fetch_assoc())
                $arr[]=$row;
            return $arr;
            }
            else{
                return 0;
            }
        }

        // ✅ Hàm INSERT, UPDATE, DELETE (không cần trả ID)
    public function themxoasua($sql) {
        $link = $this->ketnoi();
        if ($link->query($sql) === TRUE) {
            return 1;
        } else {
            echo "Lỗi SQL: " . $link->error;
            return 0;
        }
    }

    // ✅ Hàm INSERT và trả về ID vừa thêm (dùng khi thêm đơn hàng)
    public function themxoasua_layid($sql) {
        $link = $this->ketnoi();
        if ($link->query($sql) === TRUE) {
            return $link->insert_id;
        } else {
            echo "Lỗi SQL: " . $link->error;
            return 0;
        }
    }

            public function dangky($sql){
            $link=$this->ketnoi();
            if($link->query($sql)){
                return 1;
            }
            else{
                return 0;
            }
        }

    }

?>

