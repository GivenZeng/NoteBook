### 查询操作
```
var name string
   var age int
   rows, err := db.Query("select name,age from user where id = ? ", 1)
   if err != nil {
       fmt.Println(err)
   }
   //随手关闭，哪怕fun发生exception，defer也会执行
   defer rows.Close()

   //重点
   for rows.Next() {
       err := rows.Scan(&name, &age)
       if err != nil {
           fmt.Println(err)
       }
   }

   err = rows.Err()
   if err != nil {
       fmt.Println(err)
   }

   fmt.Println("name:", url, "age:", description)
```
### 单条查询
```
var name string
err = db.QueryRow("select name from user where id = ?", 222).Scan(&name)


//官方demo
id := 123
var username string
err := db.QueryRow("SELECT username FROM users WHERE id=?", id).Scan(&username)
switch {
case err == sql.ErrNoRows:
        log.Printf("No user with that ID.")
case err != nil:
        log.Fatal(err)
default:
        fmt.Printf("Username is %s\n", username)
}
```
问题：单条查询如何关闭？还是会自动关闭。官方demo中，单条查询没有close




### 空值
有时候我们并不关心值是不是Null,我们只需要吧他当一个空字符串来对待就行。这时候我们可以使用[]byte（null byte[]可以转化为空string） 或者 sql.RawBytes,
```
var col1, col2 []byte

for rows.Next() {
    // Scan the value to []byte
    err = rows.Scan(&col1, &col2)

    if err != nil {
        panic(err.Error()) // Just for example purpose. You should use proper error handling instead of panic
    }

    // Use the string value
    fmt.Println(string(col1), string(col2))
}
```

### 其他查询
```
    // Get column names
    columns, err := rows.Columns()
    if err != nil {
        panic(err.Error()) // proper error handling instead of panic in your app
    }

    // Make a slice for the values
    values := make([]sql.RawBytes, len(columns))

    // rows.Scan wants '[]interface{}' as an argument, so we must copy the
    // references into such a slice
    // See http://code.google.com/p/go-wiki/wiki/InterfaceSlice for details
    scanArgs := make([]interface{}, len(values))
    for i := range values {
        scanArgs[i] = &values[i]
    }

    // Fetch rows
    for rows.Next() {
        // get RawBytes from data
        err = rows.Scan(scanArgs...)
        if err != nil {
            panic(err.Error()) // proper error handling instead of panic in your app
        }

        // Now do something with the data.
        // Here we just print each column as a string.
        var value string
        for i, col := range values {
            // Here we can check if the value is nil (NULL value)
            if col == nil {
                value = "NULL"
            } else {
                value = string(col)
            }
            fmt.Println(columns[i], ": ", value)
        }
        fmt.Println("-----------------------------------")
    }
    if err = rows.Err(); err != nil {
        panic(err.Error()) // proper error handling instead of panic in your app
    }
```
