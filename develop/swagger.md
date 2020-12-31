从proto生成swagger
```
 protoc -I/usr/local/include -I. \
   -I$GOPATH/src \
   -I$GOPATH/src/git.umlife.net/adxmi/adn/schema \
   -I$GOPATH/src/github.com/grpc-ecosystem/grpc-gateway/third_party/googleapis \
   --swagger_out=logtostderr=true:. \
   srv.adm.proto
```
