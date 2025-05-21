using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;

namespace WebMULTI.Models
{
    public class KH_CT
    {
        webMultiDataContext db = new webMultiDataContext();
        public int MaHD { get; set; }
        public int MaKH { get; set; }
        public string HoTen { get; set; }
        public string Diachi { get; set; }
        public string sdt { get; set; }
    }
}