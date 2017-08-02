import jenkins.*
import jenkins.model.*
import hudson.*
import hudson.model.*

def instance = Jenkins.getInstance()
def job = instance.getItem('__JOB_NAME__')
Hudson.instance.getView("By project").add(job)
