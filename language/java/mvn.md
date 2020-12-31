## create project
```sh
mvn archetype:generate "-DgroupId=cn.zenggiven.flink" "-DartifactId=flink_start" "-DarchetypeArtifactId=maven-archetype-quickstart" "-DinteractiveMode=false"
```

## build
```
mvn clean package
```


## Unknown lifecycle phase "mvn".
```
第一步：mvn install
第二步：mvn compiler:compile
第三步：mvn org.apache.maven.plugins:maven-compiler-plugin:compile
第四步：mvn org.apache.maven.plugins:maven-compiler-plugin:2.0.2:compile
```