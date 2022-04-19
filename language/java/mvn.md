## create project
```sh
mvn archetype:generate "-DgroupId=cn.zenggiven.hello" "-DartifactId=hello" "-DarchetypeArtifactId=maven-archetype-quickstart" "-DinteractiveMode=false"
```

## build
在pom.xml添加打包插件
```xml
<build>
    <plugins>

        <!-- 使用maven-jar-plugin和maven-dependency-plugin插件打包 -->
        <plugin>
            <groupId>org.apache.maven.plugins</groupId>
            <artifactId>maven-jar-plugin</artifactId>
            <version>3.1.0</version>
            <configuration>
                <archive>
                    <manifest>
                        <addClasspath>true</addClasspath>
                        <classpathPrefix>lib/</classpathPrefix>
                        <mainClass>com.test.api.MyMain</mainClass>
                    </manifest>
                </archive>
            </configuration>
        </plugin>
        <plugin>
            <groupId>org.apache.maven.plugins</groupId>
            <artifactId>maven-dependency-plugin</artifactId>
            <version>3.1.1</version>
            <executions>
                <execution>
                    <id>copy-dependencies</id>
                    <phase>package</phase>
                    <goals>
                        <goal>copy-dependencies</goal>
                    </goals>
                    <configuration>
                        <outputDirectory>${project.build.directory}/lib</outputDirectory>
                    </configuration>
                </execution>
            </executions>
        </plugin>

    </plugins>
</build>
```

清除缓存并打包，然后运行最后的jar文件
```
mvn clean package
java -jar target/hello-1.0-SNAPSHOT.jar
```

## run
如果仅仅是想本地运行，无需进行打包，可用以下命令
```sh
mvn compile exec:java -Dexec.mainClass="cn.zenggiven.hello.App"
```

## 发布
mvn搜索依赖首先会在本地maven仓库进行搜索（~/.m2/repository），搜不到再到远程进行拉取。

如果我们开发的是库，我们可以使用以下命令将我们的开发的库安装到本地仓库，供本地其他项目调试、使用。（线上应该将我们的库推送到中心远程仓库）
```
mvn install
```
该命令会在当前项目打包生成一个可执行的jar文件，并生成一个库jar文件到本地仓库~/.m2/repository。

## 官方中央仓库
https://mvnrepository.com/

## Unknown lifecycle phase "mvn".
```
第一步：mvn install
第二步：mvn compiler:compile
第三步：mvn org.apache.maven.plugins:maven-compiler-plugin:compile
第四步：mvn org.apache.maven.plugins:maven-compiler-plugin:2.0.2:compile
```

pom 文件介绍：https://www.cnblogs.com/channingwong/p/12634821.html