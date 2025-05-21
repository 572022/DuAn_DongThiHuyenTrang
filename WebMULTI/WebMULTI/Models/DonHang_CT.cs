using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;

namespace WebMULTI.Models
{
    public class DonHang_CT
    {
        webMultiDataContext db = new webMultiDataContext();
        public int MaHD { get; set; }
        public int MaSP { get; set; }
        public List<string> TenSanPhams { get; set; }
        public int TongTien { get; set; }
        public DateTime NgayMua { get; set; }
        public int TinhTrangDonHang { get; set; }
        public bool DaThanhToan { get; set; }
    }
}