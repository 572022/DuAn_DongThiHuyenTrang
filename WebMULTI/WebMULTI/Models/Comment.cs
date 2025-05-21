using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;

namespace WebsiteQuanAo.Models
{
    public class Comment
    {
        public string Username { get; set; }
        public int MaSP { get; set; }
        public DateTime NgayBL { get; set; }
        public string Danhgia { get; set; }
        public string Noidung { get; set; }
    }
}