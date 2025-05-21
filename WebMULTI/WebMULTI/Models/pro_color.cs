using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;

namespace WebMULTI.Models
{
    public class pro_color
    {
        public SanPham SanPham { get; set; } = new SanPham();
        public int ProductId { get; set; }
        public string Name { get; set; }
        public bool HasCommented { get; set; }
        public string namecolor { get; set; }
        public List<BinhLuan> BinhLuans { get; set; }
        public Product_Color procductDetails { get; set; }
        public int MaSP { get; set; }
        public string Mota { get; set; }

        
        public DanhMuc DanhMuc { get; set; }
     
        public LoaiSP LoaiSP { get; set; }
      
    }

}