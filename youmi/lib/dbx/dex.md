一些关于数据库设置的工具，与业务无关

## dex.go
```
type Conf struct {
	Driver          string        `yaml:"db_driver"` // validate:"nonzero"`
	DSN             string        `yaml:"db_dsn"`    // validate:"nonzero"`
	MaxOpenConn     int           `yaml:"max_open_conn"`
	MaxIdleConn     int           `yaml:"max_idle_conn"`
	ConnMaxLifetime time.Duration `yaml:"conn_max_lifetime"`
	Debug           bool
}

func (c *Conf) New() (DB, error)
```

## dsn.go
```
type DB interface {
	Selector
	ExecContext(ctx context.Context, query string, args ...interface{}) (sql.Result, error)
	OpenConnections() int
	Close() error
}


type DBX struct {
	dbx    *sqlx.DB
	Debug  bool
	Logger alog.Logger
}
func (db *DBX) OpenConnections() int
func (db *DBX) Select(ctx context.Context, dst interface{}, query string, args ...interface{}) (bool, error)
func (db *DBX) ExecContext(ctx context.Context, query string, args ...interface{}) (sql.Result, error)
func (db *DBX) Close() error
```
