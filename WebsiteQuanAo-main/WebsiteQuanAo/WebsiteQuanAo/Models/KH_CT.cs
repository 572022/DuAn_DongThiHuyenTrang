using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;

namespace WebsiteQuanAo.Models
{
    public class KH_CT
    {
        DataQuanAoDataContext db = new DataQuanAoDataContext();
        public int MaHD { get; set; }
        public int MaKH { get; set; }
        public string HoTen { get; set; }
        public string Diachi { get; set; }
        public string sdt { get; set; }
    }
}