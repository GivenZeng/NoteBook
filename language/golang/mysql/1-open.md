### document
* https://github.com/go-sql-driver/mysql
* https://golang.org/pkg/database/sql/

### 连接
```
import (
    "database/sql"
    _ "github.com/go-sql-driver/mysql"
    "log"
)
func main() {
    db, err := sql.Open("mysql", "user:password@tcp(ip:port)/database")
    if err != nil {
        log.Println(err)
    }

    //在这里进行一些数据库操作

    defer db.Close()
}
```

### ping是否连接有效
```
err = db.Ping()
if err != nil {
    log.Println(err)
}
```
