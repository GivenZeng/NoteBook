Once是一个用于进行仅一次操作的对象

```
package main              

import (                  
        "fmt"             
        "sync"            
)                         

func main() {             
        var once sync.Once                          
        onceBody := func() {                        
                fmt.Println("Only once")            
        }                 
        done := make(chan bool)                     
        for i := 0; i < 10; i++ {                   
                go func() {                         
                        // 如果i=0，那么会执行onceBody，否则直接返回，执行下一句                         
                        once.Do(onceBody)           
                        done <- true
                }()
        }
        for i := 0; i < 10; i++ {
                <-done
        }
}

```
