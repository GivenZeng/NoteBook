在手动ctrl+c之后进行一些自定义的操作
```
package main

import (
    "fmt"
    "os"
    "os/signal"
    "syscall"
    "time" // or "runtime"
)

func cleanup() {
    fmt.Println("cleanup")
}

func main() {
    c := make(chan os.Signal, 2)
    signal.Notify(c, os.Interrupt, syscall.SIGTERM)
    go func() {
        <-c
        cleanup()
        os.Exit(1)
    }()

    for {
        fmt.Println("sleeping...")
        time.Sleep(10 * time.Second) // or runtime.Gosched() or similar per @misterbee
    }
}
```
