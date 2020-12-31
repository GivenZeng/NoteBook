### 增删改操作
```
//使用prepare可以使得效率较高
stmt, err := db.Prepare("insert into user(name,age)values(?,?)")
if err != nil {
    log.Println(err)
}

rs, err := stmt.Exec("go-test", 12)
if err != nil {
    log.Println(err)
}
//我们可以获得插入的id
id, err := rs.LastInsertId()
//可以获得影响行数
affect, err := rs.RowsAffected()
```
