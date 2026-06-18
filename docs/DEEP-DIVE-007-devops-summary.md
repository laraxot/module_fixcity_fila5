---
title: "Deep Dive: DevOps & Infrastructure Summary"
type: technical-spec
tags: [fixcity, devops, kubernetes, terraform, ci-cd]
status: draft
created: 2026-06-17
---

# Deep Dive: DevOps & Infrastructure

## 1. Infrastructure Stack (AWS)

### 1.1 Production Architecture

```
┌─────────────────────────────────────────────────────────┐
│                         EDGE                            │
│  ┌──────────────────────────────────────────────────┐  │
│  │  CloudFront CDN (SSL/TLS, DDoS protection)       │  │
│  │  • Image caching                                   │  │
│  │  • API edge caching                                │  │
│  │  • WAF rules                                       │  │
│  └────────────────┬───────────────────────────────────┘  │
└─────────────────┼───────────────────────────────────────┘
                  │
┌─────────────────┼───────────────────────────────────────┐
│               EKS │ (Kubernetes)                          │
│                 │                                       │
│  ┌──────────────┴──────────────┐ ┌────────────────────┐  │
│  │     Ingress Controller      │ │  CoreDNS, Karpenter │  │
│  │  (NGINX, SSL termination)   │ │  (Auto-scaling)     │  │
│  └──────────────┬──────────────┘ └────────────────────┘  │
│                 │                                       │
│  ┌──────────────┴──────────────┐ ┌────────────────────┐  │
│  │      App Pods (3-50)        │ │   Queue Workers    │  │
│  │  • Laravel PHP-FPM          │ │   (Horizon)        │  │
│  │  • Health checks            │ │                    │  │
│  │  • Auto-scaling (HPA)       │ │                    │  │
│  └─────────────────────────────┘ └────────────────────┘  │
└─────────────────────────────────────────────────────────┘
                  │
┌─────────────────┼───────────────────────────────────────┐
│                 DATA LAYER                              │
│                                                         │
│  ┌──────────────┐  ┌──────────────┐  ┌─────────────┐  │
│  │ RDS Postgres │  │  ElastiCache │  │    S3       │  │
│  │  (Multi-AZ)  │  │   (Redis)    │  │ (Images)    │  │
│  │  • Primary   │  │  • Sessions  │  │  • Backups  │  │
│  │  • Replica   │  │  • Queue     │  │  • Logs     │  │
│  │  • Encrypted │  │  • Cache     │  │  • CDN orig │  │
│  └──────────────┘  └──────────────┘  └─────────────┘  │
└─────────────────────────────────────────────────────────┘
```

### 1.2 Terraform Resources

```hcl
# VPC + EKS + RDS + Redis + S3
module "vpc" {
  source = "terraform-aws-modules/vpc/aws"
  cidr = "10.0.0.0/16"
  azs = ["eu-central-1a", "eu-central-1b", "eu-central-1c"]
  private_subnets = ["10.0.1.0/24", "10.0.2.0/24", "10.0.3.0/24"]
  public_subnets = ["10.0.101.0/24", "10.0.102.0/24", "10.0.103.0/24"]
  enable_nat_gateway = true
  single_nat_gateway = false  # HA in production
}

module "eks" {
  source = "terraform-aws-modules/eks/aws"
  cluster_version = "1.28"
  cluster_name = "fixcity-production"
  vpc_id = module.vpc.vpc_id
  subnet_ids = module.vpc.private_subnets
  
  eks_managed_node_groups = {
    general = {
      desired_size = 3
      min_size = 2
      max_size = 20
      instance_types = ["m6i.xlarge"]
    }
    spot = {
      desired_size = 2
      min_size = 0
      max_size = 50
      instance_types = ["m6i.large", "m5.large"]
      capacity_type = "SPOT"
    }
  }
}

module "rds" {
  source = "terraform-aws-modules/rds/aws"
  engine = "postgres"
  engine_version = "15.4"
  instance_class = "db.r6g.xlarge"
  multi_az = true
  backup_retention_period = 35
  storage_encrypted = true
  
  # Read replica for analytics
  create_db_instance_read_replica = true
}
```

---

## 2. Kubernetes Configuration

### 2.1 Key Manifests

```yaml
# Deployment with security hardening
apiVersion: apps/v1
kind: Deployment
metadata:
  name: fixcity-app
  namespace: fixcity-production
spec:
  replicas: 3
  strategy:
    rollingUpdate:
      maxSurge: 1
      maxUnavailable: 0
  template:
    spec:
      securityContext:
        runAsNonRoot: true
        runAsUser: 1000
        fsGroup: 1000
      
      initContainers:
        - name: migrations
          image: fixcity/app:latest
          command: ["php", "artisan", "migrate", "--force"]
          securityContext:
            allowPrivilegeEscalation: false
            readOnlyRootFilesystem: true
      
      containers:
        - name: app
          image: fixcity/app:latest
          ports:
            - containerPort: 8080
          resources:
            requests:
              memory: "512Mi"
              cpu: "500m"
            limits:
              memory: "1Gi"
              cpu: "1000m"
          
          livenessProbe:
            httpGet:
              path: /health/live
              port: 8080
            initialDelaySeconds: 30
            periodSeconds: 10
          
          readinessProbe:
            httpGet:
              path: /health/ready
              port: 8080
            initialDelaySeconds: 5
            periodSeconds: 5
          
          securityContext:
            allowPrivilegeEscalation: false
            readOnlyRootFilesystem: true
            capabilities:
              drop: ["ALL"]
      
      topologySpreadConstraints:
        - maxSkew: 1
          topologyKey: topology.kubernetes.io/zone
          whenUnsatisfiable: DoNotSchedule
          labelSelector:
            matchLabels:
              app: fixcity

---
# Horizontal Pod Autoscaler
apiVersion: autoscaling/v2
kind: HorizontalPodAutoscaler
metadata:
  name: fixcity-app
spec:
  scaleTargetRef:
    apiVersion: apps/v1
    kind: Deployment
    name: fixcity-app
  minReplicas: 3
  maxReplicas: 50
  metrics:
    - type: Resource
      resource:
        name: cpu
        target:
          type: Utilization
          averageUtilization: 70
    - type: Resource
      resource:
        name: memory
        target:
          type: Utilization
          averageUtilization: 80
  behavior:
    scaleUp:
      stabilizationWindowSeconds: 60
      policies:
        - type: Percent
          value: 100
          periodSeconds: 60
    scaleDown:
      stabilizationWindowSeconds: 300
      policies:
        - type: Percent
          value: 10
          periodSeconds: 60

---
# Network Policy (Zero Trust)
apiVersion: networking.k8s.io/v1
kind: NetworkPolicy
metadata:
  name: fixcity-app
spec:
  podSelector:
    matchLabels:
      app: fixcity
  policyTypes:
    - Ingress
    - Egress
  ingress:
    - from:
        - namespaceSelector:
            matchLabels:
              name: ingress-nginx
      ports:
        - protocol: TCP
          port: 8080
  egress:
    - to:
        - podSelector:
            matchLabels:
              app: postgres
      ports:
        - protocol: TCP
          port: 5432
    - to:
        - podSelector:
            matchLabels:
              app: redis
      ports:
        - protocol: TCP
          port: 6379
```

---

## 3. CI/CD Pipeline (GitHub Actions)

### 3.1 Workflow Stages

```yaml
name: Production Deploy

on:
  push:
    branches: [main]

jobs:
  # Stage 1: Security & Quality
  security:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
          tools: phpstan, pint
      
      - run: composer install
      - run: vendor/bin/pint --test
      - run: vendor/bin/phpstan analyse --memory-limit=2G
      - run: composer audit
      - uses: trufflesecurity/trufflehog@main

  # Stage 2: Testing
  test:
    runs-on: ubuntu-latest
    services:
      postgres:
        image: postgres:15
        env:
          POSTGRES_USER: test
          POSTGRES_PASSWORD: test
          POSTGRES_DB: fixcity_test
        ports:
          - 5432:5432
      redis:
        image: redis:7-alpine
        ports:
          - 6379:6379
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - run: composer install
      - run: vendor/bin/pest --coverage --min=80
      - uses: codecov/codecov-action@v3

  # Stage 3: Build & Push
  build:
    needs: [security, test]
    runs-on: ubuntu-latest
    permissions:
      id-token: write  # For OIDC
    steps:
      - uses: actions/checkout@v4
      - uses: aws-actions/configure-aws-credentials@v4
        with:
          role-to-assume: arn:aws:iam::123456789:role/github-actions
          aws-region: eu-central-1
      - uses: aws-actions/amazon-ecr-login@v2
      
      - uses: docker/build-push-action@v5
        with:
          context: .
          push: true
          tags: |
            ${{ env.ECR_REGISTRY }}/fixcity:${{ github.sha }}
            ${{ env.ECR_REGISTRY }}/fixcity:latest
          cache-from: type=gha
          cache-to: type=gha,mode=max

  # Stage 4: Deploy
  deploy:
    needs: build
    runs-on: ubuntu-latest
    environment: production  # Requires approval
    steps:
      - uses: actions/checkout@v4
      - uses: aws-actions/configure-aws-credentials@v4
        with:
          role-to-assume: arn:aws:iam::123456789:role/github-actions
          aws-region: eu-central-1
      
      - run: |
          aws eks update-kubeconfig --name fixcity-production
          
          # Update image tag
          kustomize edit set image fixcity/app=${{ env.ECR_REGISTRY }}/fixcity:${{ github.sha }}
          
          # Apply with record for rollback
          kubectl apply -k k8s/overlays/production --record
          
          # Wait for rollout
          kubectl rollout status deployment/fixcity-app -n fixcity-production --timeout=300s
          
          # Verify health
          kubectl run smoke-test --rm -i --restart=Never --image=curlimages/curl -- curl -sf http://fixcity-app.fixcity-production.svc.cluster.local/health/ready
```

---

## 4. Monitoring Stack

### 4.1 Prometheus + Grafana

```yaml
# ServiceMonitor for scraping
apiVersion: monitoring.coreos.com/v1
kind: ServiceMonitor
metadata:
  name: fixcity-metrics
spec:
  selector:
    matchLabels:
      app: fixcity
  endpoints:
    - port: metrics
      path: /metrics
      interval: 15s

---
# Grafana Dashboard (simplified)
apiVersion: integreatly.org/v1alpha1
kind: GrafanaDashboard
metadata:
  name: fixcity-dashboard
spec:
  json: |
    {
      "title": "FixCity Production",
      "panels": [
        {
          "title": "Request Rate",
          "targets": [
            {
              "expr": "rate(http_requests_total{job=\"fixcity\"}[5m])",
              "legendFormat": "{{method}} {{status}}"
            }
          ]
        },
        {
          "title": "Response Time (p99)",
          "targets": [
            {
              "expr": "histogram_quantile(0.99, rate(http_request_duration_seconds_bucket[5m]))"
            }
          ]
        },
        {
          "title": "Error Rate",
          "targets": [
            {
              "expr": "rate(http_requests_total{job=\"fixcity\",status=~\"5..\"}[5m])"
            }
          ]
        },
        {
          "title": "Queue Length",
          "targets": [
            {
              "expr": "queue_length{queue=\"default\"}"
            }
          ]
        }
      ]
    }
```

### 4.2 Alerting Rules

```yaml
# PrometheusRule
apiVersion: monitoring.coreos.com/v1
kind: PrometheusRule
metadata:
  name: fixcity-alerts
spec:
  groups:
    - name: fixcity
      rules:
        - alert: HighErrorRate
          expr: rate(http_requests_total{status=~"5.."}[5m]) > 0.05
          for: 5m
          labels:
            severity: critical
          annotations:
            summary: "High error rate detected"
            
        - alert: SlowResponses
          expr: histogram_quantile(0.99, rate(http_request_duration_seconds_bucket[5m])) > 2
          for: 5m
          labels:
            severity: warning
            
        - alert: QueueBacklog
          expr: queue_length > 1000
          for: 10m
          labels:
            severity: warning
            
        - alert: DatabaseConnectionsHigh
          expr: pg_stat_activity_count > 80
          for: 5m
          labels:
            severity: critical
```

---

## 5. Disaster Recovery

### 5.1 Backup Strategy

| Component | Method | Frequency | Retention |
|-----------|--------|-----------|-----------|
| RDS | Automated + Manual snapshots | Daily | 35 days |
| S3 | Cross-region replication | Real-time | 7 years |
| Redis | RDB snapshots | 6 hours | 7 days |
| EBS | Snapshots | Daily | 30 days |

### 5.2 Recovery Procedures

```bash
# RDS Point-in-Time Recovery
aws rds restore-db-instance-to-point-in-time \
  --source-db-instance fixcity-production \
  --target-db-instance fixcity-recovery \
  --restore-time 2024-06-17T10:00:00Z

# S3 Bucket Recovery
cd s3://fixcity-backups/production/
aws s3 sync . s3://fixcity-recovery/ --delete

# Kubernetes Disaster Recovery (Velero)
velero backup create fixcity-emergency-backup --include-namespaces fixcity-production
velero restore create --from-backup fixcity-emergency-backup
```

---

## 6. Cost Optimization

### 6.1 Monthly Cost Estimate (Production)

| Service | Specs | Monthly Cost |
|---------|-------|-------------|
| EKS | 3 nodes (m6i.xlarge) | ~€450 |
| RDS | db.r6g.xlarge Multi-AZ | ~€350 |
| ElastiCache | r6g.large cluster | ~€150 |
| S3 | 500GB + transfer | ~€50 |
| CloudFront | 1TB transfer | ~€100 |
| ALB | 2 load balancers | ~€40 |
| **Total** | | **~€1,140/mo** |

### 6.2 Cost Saving Strategies

1. **Spot Instances:** 50-70% saving on worker nodes (use for non-critical workloads)
2. **Reserved Instances:** 40% saving on RDS (1-year commitment)
3. **S3 Lifecycle:** Glacier after 90 days (60% saving)
4. **Auto-scaling:** Scale to zero for dev environments
5. **Karpenter:** Dynamic node provisioning (vs static ASG)

---

*Infrastructure based on: AWS Well-Architected Framework, Kubernetes best practices, 12-Factor App methodology.*
