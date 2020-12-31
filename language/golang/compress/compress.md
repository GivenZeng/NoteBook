### gzip
```
func NewReader(r io.Reader) (*Reader, error)
func (z *Reader) Close() error
func (z *Reader) Read(p []byte) (n int, err error)
func (z *Reader) Reset(r io.Reader) error // 重置reader，使得状态如同一个新的reader

func NewWriter(w io.Writer) *Writer  //使用默认的压缩级别
func NewWriterLevel(w io.Writer, level int) (*Writer, error) // 指定压缩级别
func (z *Writer) Close() error
func (z *Writer) Flush() error
func (z *Writer) Reset(w io.Writer)
func (z *Writer) Write(p []byte) (int, error)

```
#### Read
```
func ReadGzFile(filename string) ([]byte, error) {
    fi, err := os.Open(filename)
    if err != nil {
        return nil, err
    }
    defer fi.Close()

    fz, err := gzip.NewReader(fi)
    if err != nil {
        return nil, err
    }
    defer fz.Close()

    s, err := ioutil.ReadAll(fz)
    if err != nil {
        return nil, err
    }
    return s, nil   
}
```
