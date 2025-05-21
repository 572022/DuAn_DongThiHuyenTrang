using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using WebsiteQuanAo.Models;
namespace WebsiteQuanAo.Models
{
    public class DonHang_CT
    {
        DataQuanAoDataContext db = new DataQuanAoDataContext();
        public int MaHD { get; set; }
        public int MaSP { get; set; }
        public List<string> TenSanPhams { get; set; }
        public int TongTien { get; set; }
        public DateTime NgayMua { get; set; }
        public int TinhTrangDonHang { get; set; }
        public bool DaThanhToan {  get; set; }
        //public DonHang DonHang { get; set; }
        //public ChiTietDonHang DonHangCT { get; set; }
        //public SanPham SanPham { get; set; }


    }
}