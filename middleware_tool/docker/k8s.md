开始master节点的初始化工作，注意这边的--pod-network-cidr=10.244.0.0/16，是k8的网络插件所需要用到的配置信息，用来给node分配子网段，我这边用到的网络插件是flannel，就是这么配，其他的插件也有相应的配法，官网上都有详细的说明，具体参考这个网页

```
sudo apt install kubeadm
# 设置镜像仓库，不然kubeadm会拉取google自己的仓库
sudo kubeadm init --pod-network-cidr=10.244.0.0/16 --image-repository=registry.cn-hangzhou.aliyuncs.com/google_containers
```


启动完成会输出 以下内容，即表示本地节点已经启动完成。如果需要
```sh
Your Kubernetes control-plane has initialized successfully!
```

如果需要当前及其用户通过kubectl访问，需要进行以下设置
```sh
  mkdir -p $HOME/.kube
  sudo cp -i /etc/kubernetes/admin.conf $HOME/.kube/config
  sudo chown $(id -u):$(id -g) $HOME/.kube/config
```

如果需要部署一个pod，可以通过以下命令。配置文件可参考 https://kubernetes.io/docs/concepts/cluster-administration/addons/
```sh
kubectl apply -f [podnetwork].yaml
```

如果需要将新节点加入到本集群，需要运行以下命令。（下述密钥仅是样例）
```sh
kubeadm join 10.227.74.4:6443 --token vsv3eq.wbdf2dvnfg4rbi17 \
    --discovery-token-ca-cert-hash sha256:01585d0bd640cbaf3b608a060d8ec0e95add1a2bf7be32ca45320c076cadbe62
```

如果使用kubectl get pods -n kube-system 发现服务coredns未ready，则使用以下命令

```
wgethttps://raw.githubusercontent.com/coreos/flannel/master/Documentation/kube-flannel.yml
sudo kubectl apply -f ./kube-flannel.yml
```

 获取核心组件的状态
 ```sh
  kubectl get componentstatus
 ```


获取pod 状态
```
  kubectl get pods -n kube-system
```