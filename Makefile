BUILDDIR := ./build/
BINARY = $(BUILDDIR)Frpc

all : dir makebinary package


toolchain := $(ROOTDIR)toolchain/
CXX = $(toolchain)/bin/arm-xiaomi-linux-uclibcgnueabi-g++
CC =$(toolchain)/bin/arm-xiaomi-linux-uclibcgnueabi-gcc
LIB_DIR = -L$(ROOTDIR)lib
LD_LIBRARY_PATH = $(ROOTDIR)lib
CXXFLAGS += -I$(ROOTDIR)include/
LDFLAGS = -Wall -O2 -lxmrouter -lthrift -lssl -lcrypto -lconfig++ -ljson-c \
 -lboost_system -lboost_filesystem -lthriftnb -levent -lcurl -lz -lboost_thread \
 -lroutermain -std=c++11 -lxmrouter

dir : 
	mkdir -p $(BUILDDIR)

makebinary :
#	$(CXX) $(CXXFLAGS) JSON.cpp Tools.cpp inifile.c Frp.cpp $(LIB_DIR) $(LDFLAGS) -o $(BINARY) -L $(LIB_DIR)
	
clean:
	rm -r build

.PHONY : clean TaskWatcher

package: 
	cp start_script build/
	cp startFrpc build/
	cp -rf bin/* build/
	$(ROOTDIR)/plugin_packager_x64
